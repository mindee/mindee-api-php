#!/usr/bin/env bash
# Installs the bernard_ledit PHP extension with PIE (https://github.com/php/pie), for the PHP on PATH:
#   bash tools/install-bernard-ledit.sh               # the version composer.json pins
#   bash tools/install-bernard-ledit.sh 0.2.0         # another version, e.g. to try it before pinning it
#   bash tools/install-bernard-ledit.sh --uninstall
#
# composer.json pins the version with "conflict": {"ext-bernard_ledit": "<X.Y.Z"}, so that Composer
# refuses any other version of the extension.
#
# Env (optional):
#   PHP  PHP binary to install for (default: php on PATH, which is also PIE's default)
#
# Also removes what the former build-from-source installer left behind: a libpdfium next to the
# extension (released builds link PDFium in) and its 99-bernard_ledit.ini.
set -euo pipefail

readonly PACKAGE="mindee/bernard-ledit"
repo_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
readonly repo_root

die() {
  echo "error: $*" >&2
  exit 1
}

# Prints the version composer.json pins, from its "conflict": {"ext-bernard_ledit": "<X.Y.Z"}.
pinned_version() {
  local constraint
  # shellcheck disable=SC2016 # $argv is PHP's, not the shell's
  constraint="$("$PHP" -r 'echo json_decode(file_get_contents($argv[1]), true)["conflict"]["ext-bernard_ledit"] ?? "";' \
    "$repo_root/composer.json")"
  [[ "$constraint" =~ ^\<[[:space:]]*([0-9]+\.[0-9]+\.[0-9]+[^[:space:]]*)$ ]] ||
    die "composer.json should pin the extension as \"conflict\": {\"ext-bernard_ledit\": \"<X.Y.Z\"}; found '$constraint'"
  printf '%s\n' "${BASH_REMATCH[1]}"
}

# Runs a command, with sudo only when the file or its directory isn't writable.
priv() {
  local target="$1"
  shift
  if [ -w "$target" ] || { [ ! -e "$target" ] && [ -w "$(dirname "$target")" ]; }; then
    "$@"
  elif command -v sudo >/dev/null; then
    sudo "$@"
  else
    die "need write access to $target, and sudo isn't available"
  fi
}

# Prints the first existing directory PHP scans for extra .ini files, if any.
ini_scan_dir() {
  local raw dir separator
  local -a dirs
  raw="$(env -u PHP_INI_SCAN_DIR "$PHP" --ini 2>/dev/null |
    sed -n 's/^Scan for additional .ini files in: //p' | tr -d '\r"')"
  # PHP separates the directories with its PATH_SEPARATOR: ";" on Windows, whose paths contain ":".
  separator="$("$PHP" -r 'echo PATH_SEPARATOR;')"
  IFS="$separator" read -r -a dirs <<< "$raw"
  for dir in "${dirs[@]}"; do
    if [ -n "$dir" ] && [ "$dir" != "(none)" ] && [ -d "$dir" ]; then
      printf '%s\n' "$dir"
      return
    fi
  done
}

# Removes the files only the old build-from-source installer created.
remove_legacy_files() {
  local ext_dir lib
  ext_dir="$("$PHP" -r 'echo ini_get("extension_dir");')"
  for lib in "$ext_dir/libpdfium.so" "$ext_dir/libpdfium.dylib"; do
    if [ -e "$lib" ]; then
      echo "Removing $lib, left by the former installer"
      priv "$lib" rm -f "$lib"
    fi
  done
}

# Removes the ini file that loads the extension, so PHP doesn't warn about a missing module.
remove_ini() {
  local scan_dir ini
  scan_dir="$(ini_scan_dir)"
  [ -n "$scan_dir" ] || return 0
  ini="$scan_dir/99-bernard_ledit.ini"
  if [ -e "$ini" ]; then
    echo "Removing $ini"
    priv "$ini" rm -f "$ini"
  fi
}

# Prints the php-config matching $PHP: next to it, then on PATH, with the binary's version suffix
# first (Debian names php8.3's php-config8.3).
php_config_for() {
  local dir suffix candidate
  dir="$(dirname "$PHP")"
  suffix="$(basename "$PHP")"
  suffix="${suffix#php}"
  for candidate in "$dir/php-config$suffix" "$dir/php-config"; do
    if [ -x "$candidate" ]; then
      printf '%s\n' "$candidate"
      return
    fi
  done
  if [ -n "$suffix" ] && command -v "php-config$suffix" > /dev/null; then
    command -v "php-config$suffix"
    return
  fi
  die "no php-config for $PHP: install its development package"
}

# Prints PIE's option for targeting $PHP, when it isn't the php on PATH that PIE targets anyway.
# PIE takes the PHP binary on Windows, but php-config elsewhere.
pie_target() {
  [ -n "${PHP_FROM_ENV:-}" ] || return 0
  if [ "${OS:-}" = Windows_NT ]; then
    printf '%s\n' "--with-php-path=$PHP"
  else
    printf '%s\n' "--with-php-config=$(php_config_for)"
  fi
}

# Runs PIE. Without a terminal, as in CI, PIE refuses to use sudo itself, even passwordless: when
# the extension directory isn't writable, PIE then runs entirely under sudo, with the same PATH so
# it finds the same php.
run_pie() {
  local ext_dir
  ext_dir="$("$PHP" -r 'echo ini_get("extension_dir");')"
  if [ -t 0 ] || [ -w "$ext_dir" ] || [ "${OS:-}" = Windows_NT ]; then
    pie "$@"
  elif sudo -n true 2>/dev/null; then
    sudo env "PATH=$PATH" pie "$@"
  else
    die "$ext_dir isn't writable, and sudo needs a password; run this from a terminal"
  fi
}

# Fails unless PHP loads the extension, at the version requested when that's an exact one, and it
# can open a PDF engine.
verify() {
  local wanted="$1" installed
  # grep reads all of php's output: with -q it would stop early, and pipefail would count php's
  # resulting SIGPIPE as a failure.
  "$PHP" -m | grep -x 'bernard_ledit' > /dev/null ||
    die "the extension did not load; check: $PHP -d display_startup_errors=1 -m"
  [ "$("$PHP" -r 'BernardLedit\Pdf\PdfDocument::create(); echo "ok";' 2>/dev/null)" = ok ] ||
    die "the extension loads, but can't create a PDF; run: $PHP -r 'BernardLedit\\Pdf\\PdfDocument::create();'"
  installed="$("$PHP" -r 'echo phpversion("bernard_ledit");')"
  if [[ "$wanted" =~ ^[0-9]+\.[0-9]+\.[0-9]+ ]] && [ "$installed" != "$wanted" ]; then
    die "PHP loads bernard_ledit $installed, not $wanted; is another copy enabled in an ini file?"
  fi
  echo "bernard_ledit $installed is installed and working."
}

main() {
  PHP_FROM_ENV="${PHP:-}"
  # A bare name like php8.3 becomes a path, so that its directory and php-config can be found.
  PHP="$(command -v "${PHP:-php}" || true)"
  [ -n "$PHP" ] || die "${PHP_FROM_ENV:-php} not found"
  command -v pie >/dev/null ||
    die "PIE not found: download https://github.com/php/pie/releases/latest/download/pie.phar, make it executable and put it on PATH as 'pie'"
  if [ -n "${PHP_INI_SCAN_DIR:-}" ]; then
    echo "Warning: PHP_INI_SCAN_DIR is set ($PHP_INI_SCAN_DIR); if it holds another bernard_ledit ini, PHP loads the extension twice." >&2
  fi

  local option target=()
  option="$(pie_target)" || exit 1
  [ -z "$option" ] || target=("$option")

  if [ "${1:-}" = "--uninstall" ]; then
    run_pie uninstall ${target[@]+"${target[@]}"} "$PACKAGE"
    remove_ini
    remove_legacy_files
    echo "bernard_ledit uninstalled."
    return
  fi

  local version="${1:-}"
  if [ -z "$version" ]; then
    version="$(pinned_version)" || exit 1
  fi
  remove_legacy_files
  run_pie install ${target[@]+"${target[@]}"} "$PACKAGE:$version"
  verify "$version"
}

main "$@"
