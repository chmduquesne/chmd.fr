SRC=.
DST=www@chmd.fr:

all: push

git-save:
	git add .
	git ci -a -m "$(shell date)"
	git push --quiet

push:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(SRC) $(DST)

pull:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(DST) $(SRC)
