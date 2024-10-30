import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';

 
registerBlockType( 'cdash-bd-blocks/pricing-table', {
    title: 'Pricing table',
    icon: 'editor-table',
    category: 'cd-blocks',
    description: __('The Pricing Table block display the Membership Levels in columns and provides a link to the membership form with pre selected levels. Chamber Dashboard Payment Options plugin must be activated to use this block.', 'cdashmm'),
    example: {
    },
    attributes: {
        showDescription:{
            type: 'boolean',
            default: true,
        },
        showPerks:{
            type: 'boolean',
            default: true,
        },
        joinNowFormPage:{
            type: 'string',
            deafult:'',
        },
        levelNameFontSize:{
            type: 'number',
            default: '30',
        },
        levelNameFontColor:{
            type: 'string',
            default: '',
        },
        levelNameBckColor:{
            type: 'string',
            default: '',
        },
        levelDescFontSize:{
            type: 'number',
            default: '18',
        },
        levelDescFontColor:{
            type: 'string',
            default: '',
        },
        levelDescBckColor:{
            type: 'string',
            default: '',
        },
        levelPerksFontSize:{
            type: 'number',
            default: '18',
        },
        levelPerksFontColor:{
            type: 'string',
            default: '',
        },
        levelPerksBckColor:{
            type: 'string',
            default: '',
        },
        levelPerksTextAlign:{
            type: 'string',
            default: 'center',
        },
        levelPriceFontSize:{
            type: 'number',
            default: '25',
        },
        levelPriceFontColor:{
            type: 'string',
            default: '',
        },
        buttonName:{
            type: 'string',
            default: __('Join Now', 'cdashmm'),
        },
        buttonColor:{
            type: 'string',
            default: '',
        },
        buttonFontSize:{
            type: 'number',
            default: '15',
        },
        buttonBckColor:{
            type: 'string',
            default: '',
        },
        setPopular:{
            type: 'boolean',
            default: false,
        },
        displaySelectMemberLevels:{
            type: 'boolean',
            default: false,
        },
        membershipLevelArray:{
            type: 'array',
            deafult:'',  
        },
    },
    edit: edit,
    save() {
        // Rendering in PHP
        return null;
    },
} );