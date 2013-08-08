all: index.html

%.html: %.md
	pandoc -s -o $@ $<

clean:
	rm -f *.html

