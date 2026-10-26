#!/usr/bin/env bash
# Installs the bernard_ledit PHP extension with PIE (https://github.com/php/pie), for the PHP on PATH:
#   bash tools/install-bernard-ledit.sh               # latest stable release
#   bash tools/install-bernard-ledit.sh 1.0.0         # a given version, or a constraint like ^1.0
#   bash tools/install-bernard-ledit.sh --uninstall
#
# Env (optional):
#   PHP  PHP binary to install for (default: php on PATH)
#
# Also removes what the former build-from-source installer left behind: a libpdfium next to the
# extension (released builds link PDFium in) and its 99-bernard_ledit.ini.
set -euo pipefail

readonly PACKAGE="mindee/bernard-ledit"

die() {
  echo "error: $*" >&2
  exit 1
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
  local raw dir
  raw="$(env -u PHP_INI_SCAN_DIR "$PHP" --ini 2>/dev/null |
    sed -n 's/^Scan for additional .ini files in: //p' | tr -d '\r"')"
  IFS=':' read -r -a dirs <<< "$raw"
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

# Fails unless PHP loads the extension and it can open a PDF engine.
verify() {
  "$PHP" -m | grep -qx 'bernard_ledit' ||
    die "the extension did not load; check: $PHP -d display_startup_errors=1 -m"
  [ "$("$PHP" -r 'BernardLedit\Pdf\PdfDocument::create(); echo "ok";' 2>/dev/null)" = ok ] ||
    die "the extension loads, but can't create a PDF; run: $PHP -r 'BernardLedit\\Pdf\\PdfDocument::create();'"
  echo "bernard_ledit $("$PHP" -r 'echo phpversion("bernard_ledit");') is installed and working."
}

main() {
  PHP="${PHP:-$(command -v php || true)}"
  [ -n "$PHP" ] || die "php not found on PATH"
  command -v pie >/dev/null ||
    die "PIE not found: download https://github.com/php/pie/releases/latest/download/pie.phar, make it executable and put it on PATH as 'pie'"
  if [ -n "${PHP_INI_SCAN_DIR:-}" ]; then
    echo "Warning: PHP_INI_SCAN_DIR is set ($PHP_INI_SCAN_DIR); if it holds another bernard_ledit ini, PHP loads the extension twice." >&2
  fi

  if [ "${1:-}" = "--uninstall" ]; then
    pie uninstall --with-php-path="$PHP" "$PACKAGE"
    remove_ini
    remove_legacy_files
    echo "bernard_ledit uninstalled."
    return
  fi

  remove_legacy_files
  pie install --with-php-path="$PHP" "$PACKAGE${1:+:$1}"
  verify
}

main "$@"
