SRC=.
DST=www@chmd.fr:

all: push

push:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(SRC) $(DST)
	git add .
	git ci -a -m "$(shell date)"
	git push --quiet

pull:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(DST) $(SRC)
