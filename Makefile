SRC=.
DST=www@chmd.fr:

all: push

push:
	rsync -avz --delete --exclude=git-public-repos --exclude=.* $(SRC) $(DST)

pull:
	rsync -avz --delete --exclude=git-public-repos --exclude=.* $(DST) $(SRC)
