all: index.html gpg.html

%.html: %.md style.css
	pandoc -s -c style.css -o $@ $<

clean:
	rm -f *.html

