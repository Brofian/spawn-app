import PluginManager from "PluginManager";

import AccordionPlugin from "./components/accordion.plugin";
import BackendContentLinkPlugin from "./components/backendContentLink.plugin";
import ResizeableColumnsPlugin from "./components/resizableColumns.plugin";
import AjaxForm from "./components/ajaxForm.plugin";


PluginManager.register('accordion.plugin', AccordionPlugin, '.js-accordion');
PluginManager.register('backend_content_link.plugin', BackendContentLinkPlugin, '[data-backend-content-link]');
PluginManager.register('backend_resizeable_columns.plugin', ResizeableColumnsPlugin, 'table.js-resizable-columns');
PluginManager.register('ajax_form.plugin', AjaxForm, '[data-ajax-form]');