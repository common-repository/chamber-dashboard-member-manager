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

const edit = props => {
    const {attributes: className, setAttributes } = props;

    return [
        <div className={ props.className }>
            <div className="member_login_form">
            This block adds a member login form and member account info to your page. 
            </div>
        </div>
    ];
};

export default edit;