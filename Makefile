SRC=.
DST=www@chmd.fr:

all: push

push:
	rsync -avz --progress --delete --exclude=git-public-repos --exclude=.* $(SRC) $(DST)

pull:
	rsync -avz --progress --delete --exclude=git-public-repos --exclude=.* $(DST) $(SRC)
