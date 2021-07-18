import PluginManager from "PluginManager";

import AccordionPlugin from "./components/accordion.plugin";

PluginManager.register('Accordion.plugin', AccordionPlugin, '.js-accordion');