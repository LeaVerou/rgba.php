# rgba.php
Script for automatic generation of one pixel alpha-transparent images for non-RGBA browsers to easily emulate RGBA colors in backgrounds.

[More info](http://leaverou.me/rgba.php/)

## Updates

First, I updated your rgba() string parsing. It's not much (literally microseconds faster), but it is faster and more system efficient. (see the included script benchmark.php for direct comparison).

I have now added all 16 of the valid HTML color names to the names list, as well as added a new syntax for loading named colors (rgba.php/colorname,alpha).

