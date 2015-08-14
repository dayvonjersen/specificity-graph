# read this first

http://csswizardry.com/2014/10/the-specificity-graph/

---

![](http://var.abl.cl/specificity-graph-screenshot.png)

# prereq

 - php

 - ruby

 - gnuplot

# run it

php -f specificity.php some.css | ./eplot

# or if you're a hipster nerd

php -f specificity.php some.css --output-json

# disclaimer

I haven't tested it very thoroughly yet so use at your own risk or something

# credits

http://csswizardry.com/2014/10/the-specificity-graph/

http://www.w3.org/TR/css3-selectors/#specificity

http://stackoverflow.com/a/12868778
http://liris.cnrs.fr/christian.wolf/software/eplot/index.html

http://realworldvalidator.com/css/pseudoelements
https://gist.github.com/afabbro/3759334#gistcomment-716299
https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Mozilla_Extensions

# license

I made it GPLv2 (see LICENSE) because of that Christian Wolf guy but you can do whatever you want kid just get out of my face [:v](http://www.wtfpl.net/wp-content/uploads/2012/12/wtfpl-strip.jpg)