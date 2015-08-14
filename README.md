# read this first

http://csswizardry.com/2014/10/the-specificity-graph/

---

I'd try to write a paragraph here, but [Harry Roberts](http://csswizardry.com/) is really the best source of information on this topic.

Apparently most people's CSS looks like this:

![](http://var.abl.cl/specificity-graph-screenshot.png)

When ideal CSS looks like this:

![](http://var.abl.cl/specificity-graph-screenshot-2.png)

I never bothered to look up how specificity is actually calculated and it turns out it's not completely trivial (unless you just happen to add it to a CSS parser... maybe there's something for postcss already and I just wasted my time...shit.)

Specificity vs. position in ruleset is not the *only* thing that matters in creating a managable CSS architecture, but it's something you can quantitatively measure and control, unlike, say, modularity or extensibility.

Or *gasp* creativity.

# prereq

 - php

 - ruby

 - gnuplot

# run it

php -f specificity.php some.css | ./eplot 2>/dev/null

# or if you're a hipster nerd

php -f specificity.php some.css --output-json

# disclaimer

I haven't tested it very thoroughly yet so use at your own risk or something

# credits

http://csswizardry.com/2014/10/the-specificity-graph/

http://www.w3.org/TR/css3-selectors/#specificity

---

http://stackoverflow.com/a/12868778

http://liris.cnrs.fr/christian.wolf/software/eplot/index.html

---

http://realworldvalidator.com/css/pseudoelements

https://gist.github.com/afabbro/3759334#gistcomment-716299

https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Mozilla_Extensions

# license

GPLv2 (see LICENSE) because of that Christian Wolf guy but you can do whatever you want kid just get out of my face [:v](http://www.wtfpl.net/wp-content/uploads/2012/12/wtfpl-strip.jpg)
