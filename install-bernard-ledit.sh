#!/usr/bin/env bash
# Temporary installer for the bernard_ledit PHP extension, until it is published for PIE.
#
# Builds from source and installs into the PHP found on PATH:
#   bash tools/install-bernard-ledit.sh            # install / reinstall
#   bash tools/install-bernard-ledit.sh --uninstall
#
# Env (optional):
#   BERNARD_LEDIT_DIR  bernard-ledit checkout to build (default: ./bernard-ledit submodule)
#   PHP                PHP binary to install for (default: php on PATH)
#
# Needs: a Rust toolchain (rustup), PHP development headers (php-config), a C compiler and cmake.
set -euo pipefail

PHP="${PHP:-$(command -v php || true)}"
[ -n "$PHP" ] || { echo "php not found on PATH" >&2; exit 1; }
PHP_CONFIG="${PHP_CONFIG:-$(dirname "$PHP")/php-config}"
[ -x "$PHP_CONFIG" ] || PHP_CONFIG="$(command -v php-config || true)"

ext_dir="$("$PHP" -r 'echo ini_get("extension_dir");')"

raw_scan="$(env -u PHP_INI_SCAN_DIR "$PHP" --ini 2>/dev/null \
  | sed -n 's/^Scan for additional .ini files in: //p' | tr -d '\r"')"
scan_dir=""
IFS=':' read -r -a scan_dirs <<< "$raw_scan"
for d in "${scan_dirs[@]}"; do
  if [ -n "$d" ] && [ "$d" != "(none)" ] && [ -d "$d" ]; then scan_dir="$d"; break; fi
done
ini_file="${scan_dir:+$scan_dir/99-bernard_ledit.ini}"

if [ -n "${PHP_INI_SCAN_DIR:-}" ]; then
  echo "Warning: PHP_INI_SCAN_DIR is set ($PHP_INI_SCAN_DIR). If it contains another" >&2
  echo "bernard_ledit ini, PHP will try to load the extension twice. Unset it or remove that file." >&2
fi

# Use sudo only where the target directory isn't writable.
# Clean non-breaking spaces and validate privilege helper
priv() {
  local target="$1"
  shift
  if [ -e "$target" ] && [ -w "$target" ]; then
    "$@"
  elif [ ! -e "$target" ] && [ -d "$(dirname "$target")" ] && [ -w "$(dirname "$target")" ]; then
    "$@"
  elif command -v sudo >/dev/null 2>&1; then
    sudo "$@"
  else
    echo "Error: Need write permissions for $target, but sudo is not available." >&2
    return 1
  fi
}

# --- preflight ---------------------------------------------------------------
command -v rustup >/dev/null || { echo "rustup not found: install Rust from https://rustup.rs" >&2; exit 1; }
[ -n "$PHP_CONFIG" ] && [ -x "$PHP_CONFIG" ] || {
  echo "php-config not found: install your PHP's development package (e.g. php-devel / php8.x-dev)" >&2; exit 1; }
[ -n "$scan_dir" ] && [ "$scan_dir" != "(none)" ] || {
  echo "This PHP has no ini scan directory; add 'extension=bernard_ledit' to php.ini manually." >&2; exit 1; }

repo_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
src="${BERNARD_LEDIT_DIR:-$repo_root/bernard-ledit}"
if [ -z "${BERNARD_LEDIT_DIR:-}" ] && [ ! -f "$src/Cargo.toml" ]; then
  git -C "$repo_root" submodule update --init --recursive bernard-ledit
fi
[ -f "$src/Cargo.toml" ] || { echo "No bernard-ledit checkout at $src" >&2; exit 1; }

case "$(uname -s)-$(uname -m)" in
  Linux-x86_64)          pdfium_target=linux-x64;   lib_ext=so ;;
  Linux-aarch64)         pdfium_target=linux-arm64; lib_ext=so ;;
  Darwin-arm64)          pdfium_target=mac-arm64;   lib_ext=dylib ;;
  Darwin-x86_64)         pdfium_target=mac-x64;     lib_ext=dylib ;;
  *) echo "Unsupported platform $(uname -s)-$(uname -m)" >&2; exit 1 ;;
esac

# --- build -------------------------------------------------------------------
echo "Building bernard_ledit for $("$PHP" -r 'echo PHP_VERSION, PHP_ZTS ? " ZTS" : " NTS";') ..."
# Make ext-php-rs build against this exact PHP, not another one on the machine.
# rustup reads rust-toolchain.toml from the working directory, so build from inside the checkout.
(
  cd "$src"
  rustup toolchain install >/dev/null 2>&1 || true   # installs the pinned toolchain if missing
  PHP="$PHP" PHP_CONFIG="$PHP_CONFIG" cargo build --release -p bernard-ledit-php
)

built_ext="$src/target/release/libbernard_ledit_php.$lib_ext"
rel="$(tr -d '[:space:]' < "$src/core/pdfium-release")"
pdfium_lib="$src/core/.pdfium_cache/${rel//\//_}/$pdfium_target/lib/libpdfium.$lib_ext"
[ -f "$built_ext" ]  || { echo "Build output not found: $built_ext" >&2; exit 1; }
[ -f "$pdfium_lib" ] || { echo "PDFium library not found: $pdfium_lib" >&2; exit 1; }
# --- install binaries ---
priv "$ext_dir" install -m 0755 "$built_ext"  "$ext_dir/bernard_ledit.so"
priv "$ext_dir" install -m 0755 "$pdfium_lib" "$ext_dir/libpdfium.$lib_ext"

# --- register extension ---
if [ -n "$ini_file" ]; then
  echo "extension=bernard_ledit" | priv "$ini_file" tee "$ini_file" >/dev/null
else
  echo "Notice: No INI scan directory found. Add 'extension=bernard_ledit' to your main php.ini."
fi

# --- verify ------------------------------------------------------------------
"$PHP" -m | grep -qi 'bernard.ledit' || {
  echo "The extension did not load. Check: $PHP -d display_startup_errors=1 -m" >&2; exit 1; }

smoke='BernardLedit\Pdf\PdfDocument::create(); echo "ok";'
if [ "$(env -u PDFIUM_PATH "$PHP" -r "$smoke" 2>/dev/null)" = "ok" ]; then
  echo "bernard_ledit installed; PDFium found automatically."
elif [ "$(PDFIUM_PATH="$ext_dir/libpdfium.$lib_ext" "$PHP" -r "$smoke" 2>/dev/null)" = "ok" ]; then
  echo "bernard_ledit installed. This build still needs PDFIUM_PATH; add this to your shell profile:"
  echo "  export PDFIUM_PATH=\"$ext_dir/libpdfium.$lib_ext\""
else
  echo "Extension loads, but PDFium could not be bound. Run the smoke test for details:" >&2
  echo "  PDFIUM_PATH=\"$ext_dir/libpdfium.$lib_ext\" $PHP -r '$smoke'" >&2
  exit 1
fi
