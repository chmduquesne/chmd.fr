SRC=.
DST=www@chmd.fr:

all: push

push:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(SRC) $(DST)

pull:
	rsync -avz --progress --delete --exclude-from=.rsync.exclude $(DST) $(SRC)
