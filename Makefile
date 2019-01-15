all: clean cp docs/index.html docs/gpg.html

cp:
	cp files/* docs

docs/%.html: %.md
	pandoc -s -c style.css -H header-links -o $@ $<

clean:
	rm -rf docs/*
