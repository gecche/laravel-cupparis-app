
echo "inizio rsync"
rsync -atzv --no-o --no-g  --no-links --exclude-from=./scripts/rsync/excludersync.txt  . ../studio55
#chown -R forge:forge /home/forge/sviluppolaravel.notificheweb.it
#chown -R forge:forge /home/forge/sviluppolaravel.notificheweb.it/public
#chown -R forge:forge /home/forge/sviluppolaravel.notificheweb.it/storage
echo "rsync terminato"
