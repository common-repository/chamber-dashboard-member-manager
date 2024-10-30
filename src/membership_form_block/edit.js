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
    FontSizePicker 
} from '@wordpress/components';

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
} from '@wordpress/block-editor';

import { withSelect, widthDispatch } from '@wordpress/data';

const {
    withState
} = wp.compose;

const mmSettingsPage = wpAjax.wpurl+'/wp-admin/admin.php?page=cd-settings&tab=payments';
const bdSettingsPage = wpAjax.wpurl+'/wp-admin/admin.php?page=cd-settings';
const customFieldsHelpText = <p>{__('You can create new custom fields in the ')}<a href={bdSettingsPage}>{__('Business Directory Settings')}</a></p>;
const formSettingsLink = <p>{__('You can change the form settings ')}<a href={mmSettingsPage}>{__('here')}</a></p>;
//const formSettingsLink = <p>{__('You can change the form settings ')}<a href={mmSettingsPage}>{(__('here')}</a></p>;

const actionOptions = [
    { label: 'New Member Signup', value: 'signup' },
    { label: 'Renewal Form', value: 'renewal' },
];


var fetchCustomFields = wpAjax.wpurl+'/wp-admin/admin-ajax.php?action=cdash_custom_fields';
if(fetchCustomFields.trim()==''){
    //console.log("Nothing Found");
}
const customFieldsOptions = [];

wp.apiFetch({url: fetchCustomFields}).then(posts => {
    if(posts.length == 0){
        customFieldsOptions.push({label: 'No custom fields found', value: null});
    }else{
        customFieldsOptions.push({label: 'Select one or more custom fields', value: null});
    }
    //console.log(posts);
    jQuery.each( posts, function( key, val ) {
        customFieldsOptions.push({label: val.name, value: val.name});
    });
}).catch(

);

const edit = props => {
    const {attributes: {action, customFieldsArray, busDetailsSectionTitle, customFieldsSectionTitle, addDescriptionField, addLogoUpload}, className, setAttributes } = props;

    const inspectorControls = (
        <InspectorControls key="inspector">
            <PanelBody title={ __( 'Membership Form Options' )}>
                <PanelRow>
                    <RadioControl
                        label={__("Membership Form Action")}
                        selected={ action }
                        options={ actionOptions }
                        onChange={ ( nextValue ) =>
                            setAttributes( {action: nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    <SelectControl 
                        multiple
                        label = {__("Display Custom Fields")}
                        value = {customFieldsArray}
                        options = {customFieldsOptions}
                        onChange={ ( nextValue ) =>
                            setAttributes( {customFieldsArray: nextValue } )
                        }
                        help={customFieldsHelpText}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__("Business Details Section Title")}
                        value={ busDetailsSectionTitle }
                        onChange={ ( nextValue ) =>
                            setAttributes( { busDetailsSectionTitle:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__("Custom Fields Section Title")}
                        value={ customFieldsSectionTitle }
                        onChange={ ( nextValue ) =>
                            setAttributes( { customFieldsSectionTitle:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={ __( 'Add the description field to the form' ) }
                        checked={ addDescriptionField }
                        onChange={ ( nextValue ) =>
                            setAttributes( { addDescriptionField:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={ __( 'Add logo upload option to the form' ) }
                        checked={ addLogoUpload }
                        onChange={ ( nextValue ) =>
                            setAttributes( { addLogoUpload:  nextValue } )
                        }
                    />
                </PanelRow>
                <PanelRow>
                    {formSettingsLink}
                </PanelRow>
            </PanelBody>

            
        </InspectorControls>
    );

    return [
        <div className={ props.className }>
            { inspectorControls }
            <div className="membership_form">
                This block adds the Chamber Dashboard membership or renewal form to your page.
            </div>
        </div>
    ];
};
export default edit;