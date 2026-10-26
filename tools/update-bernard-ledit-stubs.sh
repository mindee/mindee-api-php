#!/usr/bin/env bash
# Downloads the PHPStan stubs of bernard-ledit's latest release (pre-releases excluded) to
# stubs/bernard_ledit.stubs.php. Usage: update-bernard-ledit-stubs.sh [tag], e.g. v1.2.0 to pick one.
set -euo pipefail

readonly REPO="mindee/bernard-ledit"
readonly OUT="stubs/bernard_ledit.stubs.php"

die() {
  echo "error: $*" >&2
  exit 1
}

# GitHub redirects /releases/latest to the newest release that is neither a draft nor a pre-release.
latest_tag() {
  local url
  url="$(curl -fsSLI -o /dev/null -w '%{url_effective}' "https://github.com/$REPO/releases/latest")" ||
    die "could not reach GitHub"
  [[ "$url" == */releases/tag/* ]] || die "$REPO has no release yet (pre-releases don't count)"
  printf '%s\n' "${url##*/}"
}

main() {
  local tag="${1:-}"
  [ -n "$tag" ] || tag="$(latest_tag)"
  mkdir -p "$(dirname "$OUT")"
  curl -fsSL -o "$OUT.tmp" "https://raw.githubusercontent.com/$REPO/$tag/bindings/php/bernard_ledit.stubs.php" ||
    { rm -f "$OUT.tmp"; die "no stubs found for $tag"; }
  mv "$OUT.tmp" "$OUT"
  echo "Updated $OUT to bernard-ledit $tag"
}

main "$@"
