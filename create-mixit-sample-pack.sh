#!/bin/sh

MIXIT=$1
MIXITDIR=$2
SMAPLEPACK="$MIXITDIR/smaplepack-quixit-$MIXIT.zip"
TORRENTSDIR=/home/webroot/tracker.bl0rg.net/torrents
FILESDIR=/home/webroot/tracker.bl0rg.net/files
SMAPLETORRENT="$TORRENTSDIR/smaplepack-quixit-$MIXIT.zip.torrent"
TRACKERURL=http://tracker.bl0rg.net:6969/announce

TMPFILE=`tempfile`.zip

rm "$SMAPLEPACK"
zip -r "$TMPFILE" "$MIXITDIR"
mv "$TMPFILE" "$SMAPLEPACK"
# btmakemetafile "$TRACKERURL" "$SMAPLEPACK" \
#        --comment "bl0rg serves smaplepacks" \
#        --target "$SMAPLETORRENT"
# ln -sf "$SMAPLEPACK" "$TORRENTSDIR"
# ln -sf "$SMAPLEPACK" "$FILESDIR"