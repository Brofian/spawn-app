import PluginManager from "PluginManager";

import AccordionPlugin from "./components/accordion.plugin";
import BackendContentLinkPlugin from "./components/backendContentLink.plugin";
import ResizeableColumnsPlugin from "./components/resizableColumns.plugin";
import AjaxForm from "./components/ajaxForm.plugin";
import SeoUrlPreviewPlugin from "./components/seoUrlPreview.plugin"
import CustomSelect from "./components/customSelect.plugin";
import EntitySelect from "./components/entitySelect.plugin";


PluginManager.register('accordion.plugin', AccordionPlugin, '.js-accordion');
PluginManager.register('backend_content_link.plugin', BackendContentLinkPlugin, '[data-backend-content-link], a[href]');
PluginManager.register('backend_resizeable_columns.plugin', ResizeableColumnsPlugin, 'table.js-resizable-columns');
PluginManager.register('ajax_form.plugin', AjaxForm, '[data-ajax-form]');
PluginManager.register('seo_url_preview.plugin', SeoUrlPreviewPlugin, '[data-seo-url-editor]');
PluginManager.register('custom_select.plugin', CustomSelect, 'select');
PluginManager.register('entity_select.plugin', EntitySelect, '[data-entity-select]');