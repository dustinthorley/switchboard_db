( function( blocks, editor, element ) {

    var el = element.createElement;

    blocks.registerBlockType( 'switchboard/stage', {
        title: 'Switchboard Business Stage',
        icon: 'admin-comments',
        category: 'common',
        attributes: {
            title: {
                type: 'string',
                default: 'Stage Title',
            },
            description: {
                type: 'string',
                default: 'Stage description.',
            },
            quote: {
                type: 'string',
                default: 'Stage quote',
            },
            button: {
                type: 'string',
                default: 'See resources',
            },
        },
        edit: function( props ) {
            return (
                el('div', {className: props.className},
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'stage',
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'h3',
                            className: 'h3',
                            value: props.attributes.title,
                            onChange: function( content ) {
                                props.setAttributes( {title: content } );
                            }
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'p',
                            className: 'paragraph-3',
                            value: props.attributes.description,
                            onChange: function( content ) {
                                props.setAttributes( {description: content } );
                            }
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'div-block-5',
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'quote_block',
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'p',
                            className: 'paragraph-2',
                            value: props.attributes.quote,
                            onChange: function( content ) {
                                props.setAttributes( {quote: content } );
                            }
                        }
                    ),
                    el(
                        editor.RichText,
                        {
                            tagName: 'a',
                            className: 'button w-button',
                            value: props.attributes.button,
                            onChange: function( content ) {
                                props.setAttributes( {button: content } );
                            }
                        }
                    ),
                )
            );
        },
        save: function( props ) {
            return (
                el( 'div', {className: props.className },
                    el( editor.RichText.Content, {
                        tagName: 'p',
                        className: 'h3',
                        value: props.attributes.title,
                    }),
                    el( editor.RichText.Content, {
                        tagName: 'p',
                        className: 'paragraph-3',
                        value: props.attributes.description,
                    }),
                    el( editor.RichText.Content, {
                        tagName: 'p',
                        className: 'paragraph-2',
                        value: props.attributes.quote,
                    }),
                    el( 'button', {className: 'button w-button' },
                        props.attributes.button
                    )
                )
            );
        },
    } );
} )( window.wp.blocks, window.wp.editor, window.wp.element );