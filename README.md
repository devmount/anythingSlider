anythingSlider
==============

A Plugin for moziloCMS 2.0

Generates a slideshow filled with various content like text, images, html and moziloCMS syntax.

## Installation
#### With moziloCMS installer
To add (or update) a plugin in moziloCMS, go to the backend tab *Plugins* and click the item *Manage Plugins*. Here you can choose the plugin archive file (note that it has to be a ZIP file with exactly the same name the plugin has) and click *Install*. Now the anythingSlider plugin is listed below and can be activated.

#### Manually
Installing a plugin manually requires FTP Access.
- Upload unpacked plugin folder into moziloCMS plugin directory: ```/<moziloroot>/plugins/```
- Set default permissions (chmod 777 for folders and 666 for files)
- Go to the backend tab *Plugins* and activate the now listed new anythingSlider plugin

## Syntax
```
{anythingSlider|<id>|<config>|<content>}
```
Inserts the anythingSlider.

1. Parameter ```<id>```: A unique name für the slider (important for multiple sliders on one page or in the template).
2. Parameter ```<config>```: Slider Configuration. Officially there are at least 59 single configuration options. A detailed documentation can be found on the developers page: https://github.com/CSS-Tricks/AnythingSlider/wiki/Setup#wiki-options
3. Parameter ```<content>```: Content of the slider. The single slides are separated by ```|```. Any content elements are possible.

## License
This Plugin is distributed under *GNU General Public License, Version 3* (see LICENSE) or, at your choice, any further version.

## Documentation
A detailed documentation and demo can be found here:  
https://github.com/devmount-mozilo/anythingSlider/wiki/Dokumentation (german)
