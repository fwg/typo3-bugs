CKEDITOR.plugins.add('custom_element', {
    requires: 'widget',
    icons: 'custom_element',
    init: function (editor) {
        editor.widgets.add('custom_element', {
            pathName: 'CustomElement',
            button: 'Insert a custom element',

            allowedContent: 'custom-element',
            requiredContent: 'custom-element',

            template: '<custom-element>Foo</custom-element>',

            init: function () {
            },

            upcast: function (element) {
                return element.name === 'custom-element';
            },

            data: function () {
            }
        });
    }
});
