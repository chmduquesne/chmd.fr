all: index.html

%.html: %.md style.css
	pandoc -s -c style.css -H header -o $@ $<

clean:
	rm -f *.html

