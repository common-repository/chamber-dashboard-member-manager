import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import { SelectControl, 
    Toolbar,
    Button,
    Tooltip,
    PanelBody,
    PanelRow,
    FormToggle,
    TextControl, 
    ToggleControl,
    ToolbarGroup,
    ColorPicker,
    Disabled, 
    RadioControl,
    RangeControl,
    FontSizePicker } from '@wordpress/components';

    import {
        RichText,
        AlignmentToolbar,
        BlockControls,
        BlockAlignmentToolbar,
        InspectorControls,
        InnerBlocks,
        withColors,
        PanelColorSettings,
        getColorClassName,
        ColorPalette,
    } from '@wordpress/editor'
    ;
import { withSelect, widthDispatch } from '@wordpress/data';

const {
    withState
} = wp.compose;

const joinNowFormPageOptions = [
    { label: 'Select the page with the membership form.', value: null }
];

wp.apiFetch({path: "/cdashmm/v2/pages"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        joinNowFormPageOptions.push({label: val.title, value: val.slug});
    });
});

const membershipLevelOptions = [
    { label: 'Select one or more Membersihp Levels', value: null }
];

wp.apiFetch({path: "/wp/v2/membership_level?per_page=100"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        membershipLevelOptions.push({label: val.name, value: val.slug});
    });
});

const fontSizes = [
    {
        name: __( 'Small' ),
        slug: 'small',
        size: 12,
    },
    {
        name: __( 'Medium' ),
        slug: 'small',
        size: 18,
    },
    {
        name: __( 'Big' ),
        slug: 'big',
        size: 26,
    },
];
const titleFallbackFontSize = 30;

const descFallbackFontSize = 18;

const perksFallbackFontSize = 18;

const priceFallbackFontSize = 25;

const buttonFallbackFontSize = 15;

const edit = props => {
    const {attributes: { showDescription, showPerks, joinNowFormPage, levelNameFontSize, levelNameFontColor, levelNameBckColor, levelDescFontSize, levelDescFontColor, levelDescBckColor, levelPerksFontSize, levelPerksFontColor, levelPerksBckColor, levelPerksTextAlign, levelPriceFontSize, levelPriceFontColor, buttonName, buttonColor, buttonFontSize, buttonBckColor, setPopular, displaySelectMemberLevels,  membershipLevelArray, }, className, setAttributes } = props;

    const setjoinNowFormPage = joinNowFormPage => {
        props.setAttributes( { joinNowFormPage} );
    };

    const setMembershipLevel = membershipLevelArray => {
        props.setAttributes( { membershipLevelArray} );
    };

    const inspectorControls = (
        <InspectorControls key="inspector">
            <PanelBody title={ __( 'Pricing Table Options' )} initialOpen={ false }>
                <PanelRow>
                    <ToggleControl
                        label={ __( 'Show/hide the memberhsip level description' ) }
                        checked={ showDescription }
                        onChange={ ( nextValue ) =>
                            setAttributes( { showDescription:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={ __( 'Show/hide the memberhsip level perks' ) }
                        checked={ showPerks }
                        onChange={ ( nextValue ) =>
                            setAttributes( { showPerks:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                <SelectControl 
                    label = "Membership Form Page"
                    value = {joinNowFormPage}
                    options = {joinNowFormPageOptions}
                    onChange = {setjoinNowFormPage}
                />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Title' )} initialOpen={ false }>
                <PanelRow><label>Title Font Size</label></PanelRow>
                <PanelRow>
                    <FontSizePicker
                        fontSizes={ fontSizes }
                        value={ levelNameFontSize }
                        fallbackFontSize={ titleFallbackFontSize }
                        //withSlider= "true"
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelNameFontSize:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Title Font Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelNameFontColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelNameFontColor:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Title Background Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelNameBckColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelNameBckColor:  nextValue } )
                        }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Description' )} initialOpen={ false }>
                <PanelRow><label>Font Size</label></PanelRow>
                <PanelRow>
                    <FontSizePicker
                        fontSizes={ fontSizes }
                        value={ levelDescFontSize }
                        fallbackFontSize={ descFallbackFontSize }
                        //withSlider= "true"
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelDescFontSize:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Font Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelDescFontColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelDescFontColor:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Background Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelDescBckColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelDescBckColor:  nextValue } )
                        }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Perks' )} initialOpen={ false }>
                <PanelRow><label>Text Alignment</label></PanelRow>
                <PanelRow>
                    <AlignmentToolbar
                        value={ levelPerksTextAlign }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPerksTextAlign:  nextValue } )
                        }
                    />
                </PanelRow>        
                <PanelRow><label>Font Size</label></PanelRow>
                <PanelRow>
                    <FontSizePicker
                        fontSizes={ fontSizes }
                        value={ levelPerksFontSize }
                        fallbackFontSize={ perksFallbackFontSize }
                        //withSlider= "true"
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPerksFontSize:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Font Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelPerksFontColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPerksFontColor:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Background Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelPerksBckColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPerksBckColor:  nextValue } )
                        }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Price' )} initialOpen={ false }>
                <PanelRow><label>Font Size</label></PanelRow>
                <PanelRow>
                    <FontSizePicker
                        fontSizes={ fontSizes }
                        value={ levelPriceFontSize }
                        fallbackFontSize={ priceFallbackFontSize }
                        //withSlider= "true"
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPriceFontSize:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Font Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ levelPriceFontColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( {levelPriceFontColor:  nextValue } )
                        }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Button' )} initialOpen={ false }>
                <PanelRow>
                    <TextControl
                        label="Button Name"
                        value={ buttonName }
                        onChange={ ( nextValue ) =>
                            setAttributes( { buttonName:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Font Size</label></PanelRow>
                <PanelRow>
                    <FontSizePicker
                        fontSizes={ fontSizes }
                        value={ buttonFontSize }
                        fallbackFontSize={ buttonFallbackFontSize }
                        //withSlider= "true"
                        onChange={ ( nextValue ) =>
                            setAttributes( {buttonFontSize:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Button Text Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ buttonColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( { buttonColor:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow><label>Button Background Color</label></PanelRow>
                <PanelRow>
                    <ColorPalette
                        value={ buttonBckColor }
                        onChange={ ( nextValue ) =>
                            setAttributes( { buttonBckColor:  nextValue } )
                        }
                    />
                </PanelRow>
            </PanelBody>
        </InspectorControls>
    );

    return [
        <div className={ props.className }>
            <ServerSideRender
                block="cdash-bd-blocks/pricing-table"
                attributes = {props.attributes}
            />
            { inspectorControls }
            <div className="pricing_table">
                
            </div>
        </div>
    ];
};

export default edit;