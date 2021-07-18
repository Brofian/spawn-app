import PluginManager from "PluginManager";

import AccordionPlugin from "./components/accordion.plugin";
import SidebarPlugin from "./components/sidebar.plugin";

PluginManager.register('Accordion.plugin', AccordionPlugin, '.js-accordion');
PluginManager.register('Sidebar.plugin', SidebarPlugin, '.backend_sidebar');