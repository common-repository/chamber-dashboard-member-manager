!function(e){var t={};function l(n){if(t[n])return t[n].exports;var o=t[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,l),o.l=!0,o.exports}l.m=e,l.c=t,l.d=function(e,t,n){l.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,t){if(1&t&&(e=l(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(l.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)l.d(n,o,function(t){return e[t]}.bind(null,o));return n},l.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(t,"a",t),t},l.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},l.p="",l(l.s=10)}([function(e,t){!function(){e.exports=this.wp.element}()},function(e,t){!function(){e.exports=this.wp.components}()},function(e,t){!function(){e.exports=this.wp.i18n}()},function(e,t){!function(){e.exports=this.wp.editor}()},function(e,t){!function(){e.exports=this.wp.blocks}()},function(e,t){!function(){e.exports=this.wp.serverSideRender}()},function(e,t){!function(){e.exports=this.wp.data}()},function(e,t){!function(){e.exports=this.wp.date}()},function(e,t){!function(){e.exports=this.wp.compose}()},function(e,t){!function(){e.exports=this.wp.blockEditor}()},function(e,t,l){"use strict";l.r(t);var n=l(0),o=l(5),a=l.n(o),c=l(2),r=l(1),i=l(3),u=(l(6),wp.compose.withState,[{label:"Select the page with the membership form.",value:null}]);wp.apiFetch({path:"/cdashmm/v2/pages"}).then((function(e){jQuery.each(e,(function(e,t){u.push({label:t.title,value:t.slug})}))}));var s=[{label:"Select one or more Membersihp Levels",value:null}];wp.apiFetch({path:"/wp/v2/membership_level?per_page=100"}).then((function(e){jQuery.each(e,(function(e,t){s.push({label:t.name,value:t.slug})}))}));var b=[{name:Object(c.__)("Small"),slug:"small",size:12},{name:Object(c.__)("Medium"),slug:"small",size:18},{name:Object(c.__)("Big"),slug:"big",size:26}],m=function(e){var t=e.attributes,l=t.showDescription,o=t.showPerks,s=t.joinNowFormPage,m=t.levelNameFontSize,p=t.levelNameFontColor,d=t.levelNameBckColor,j=t.levelDescFontSize,O=t.levelDescFontColor,f=t.levelDescBckColor,h=t.levelPerksFontSize,g=t.levelPerksFontColor,v=t.levelPerksBckColor,E=t.levelPerksTextAlign,P=t.levelPriceFontSize,w=t.levelPriceFontColor,C=t.buttonName,y=t.buttonColor,F=t.buttonFontSize,_=t.buttonBckColor,k=(t.setPopular,t.displaySelectMemberLevels,t.membershipLevelArray,e.className,e.setAttributes),S=Object(n.createElement)(i.InspectorControls,{key:"inspector"},Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Pricing Table Options"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.ToggleControl,{label:Object(c.__)("Show/hide the memberhsip level description"),checked:l,onChange:function(e){return k({showDescription:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.ToggleControl,{label:Object(c.__)("Show/hide the memberhsip level perks"),checked:o,onChange:function(e){return k({showPerks:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.SelectControl,{label:"Membership Form Page",value:s,options:u,onChange:function(t){e.setAttributes({joinNowFormPage:t})}}))),Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Title"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Title Font Size")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.FontSizePicker,{fontSizes:b,value:m,fallbackFontSize:30,onChange:function(e){return k({levelNameFontSize:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Title Font Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:p,onChange:function(e){return k({levelNameFontColor:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Title Background Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:d,onChange:function(e){return k({levelNameBckColor:e})}}))),Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Description"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Size")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.FontSizePicker,{fontSizes:b,value:j,fallbackFontSize:18,onChange:function(e){return k({levelDescFontSize:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:O,onChange:function(e){return k({levelDescFontColor:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Background Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:f,onChange:function(e){return k({levelDescBckColor:e})}}))),Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Perks"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Text Alignment")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.AlignmentToolbar,{value:E,onChange:function(e){return k({levelPerksTextAlign:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Size")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.FontSizePicker,{fontSizes:b,value:h,fallbackFontSize:18,onChange:function(e){return k({levelPerksFontSize:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:g,onChange:function(e){return k({levelPerksFontColor:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Background Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:v,onChange:function(e){return k({levelPerksBckColor:e})}}))),Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Price"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Size")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.FontSizePicker,{fontSizes:b,value:P,fallbackFontSize:25,onChange:function(e){return k({levelPriceFontSize:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:w,onChange:function(e){return k({levelPriceFontColor:e})}}))),Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Button"),initialOpen:!1},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.TextControl,{label:"Button Name",value:C,onChange:function(e){return k({buttonName:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Font Size")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.FontSizePicker,{fontSizes:b,value:F,fallbackFontSize:15,onChange:function(e){return k({buttonFontSize:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Button Text Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:y,onChange:function(e){return k({buttonColor:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)("label",null,"Button Background Color")),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(i.ColorPalette,{value:_,onChange:function(e){return k({buttonBckColor:e})}}))));return[Object(n.createElement)("div",{className:e.className},Object(n.createElement)(a.a,{block:"cdash-bd-blocks/pricing-table",attributes:e.attributes}),S,Object(n.createElement)("div",{className:"pricing_table"}))]},p=l(4);l(7),l(8);Object(p.registerBlockType)("cdash-bd-blocks/pricing-table",{title:"Pricing table",icon:"editor-table",category:"cd-blocks",description:Object(c.__)("The Pricing Table block display the Membership Levels in columns and provides a link to the membership form with pre selected levels. Chamber Dashboard Payment Options plugin must be activated to use this block.","cdashmm"),example:{},attributes:{showDescription:{type:"boolean",default:!0},showPerks:{type:"boolean",default:!0},joinNowFormPage:{type:"string",deafult:""},levelNameFontSize:{type:"number",default:"30"},levelNameFontColor:{type:"string",default:""},levelNameBckColor:{type:"string",default:""},levelDescFontSize:{type:"number",default:"18"},levelDescFontColor:{type:"string",default:""},levelDescBckColor:{type:"string",default:""},levelPerksFontSize:{type:"number",default:"18"},levelPerksFontColor:{type:"string",default:""},levelPerksBckColor:{type:"string",default:""},levelPerksTextAlign:{type:"string",default:"center"},levelPriceFontSize:{type:"number",default:"25"},levelPriceFontColor:{type:"string",default:""},buttonName:{type:"string",default:Object(c.__)("Join Now","cdashmm")},buttonColor:{type:"string",default:""},buttonFontSize:{type:"number",default:"15"},buttonBckColor:{type:"string",default:""},setPopular:{type:"boolean",default:!1},displaySelectMemberLevels:{type:"boolean",default:!1},membershipLevelArray:{type:"array",deafult:""}},edit:m,save:function(){return null}});var d=l(9),j=(wp.compose.withState,wpAjax.wpurl+"/wp-admin/admin.php?page=cd-settings&tab=payments"),O=wpAjax.wpurl+"/wp-admin/admin.php?page=cd-settings",f=Object(n.createElement)("p",null,Object(c.__)("You can create new custom fields in the "),Object(n.createElement)("a",{href:O},Object(c.__)("Business Directory Settings"))),h=Object(n.createElement)("p",null,Object(c.__)("You can change the form settings "),Object(n.createElement)("a",{href:j},Object(c.__)("here"))),g=[{label:"New Member Signup",value:"signup"},{label:"Renewal Form",value:"renewal"}],v=wpAjax.wpurl+"/wp-admin/admin-ajax.php?action=cdash_custom_fields";v.trim();var E=[];wp.apiFetch({url:v}).then((function(e){0==e.length?E.push({label:"No custom fields found",value:null}):E.push({label:"Select one or more custom fields",value:null}),jQuery.each(e,(function(e,t){E.push({label:t.name,value:t.name})}))})).catch();var P=function(e){var t=e.attributes,l=t.action,o=t.customFieldsArray,a=t.busDetailsSectionTitle,i=t.customFieldsSectionTitle,u=t.addDescriptionField,s=t.addLogoUpload,b=(e.className,e.setAttributes),m=Object(n.createElement)(d.InspectorControls,{key:"inspector"},Object(n.createElement)(r.PanelBody,{title:Object(c.__)("Membership Form Options")},Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.RadioControl,{label:Object(c.__)("Membership Form Action"),selected:l,options:g,onChange:function(e){return b({action:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.SelectControl,{multiple:!0,label:Object(c.__)("Display Custom Fields"),value:o,options:E,onChange:function(e){return b({customFieldsArray:e})},help:f})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.TextControl,{label:Object(c.__)("Business Details Section Title"),value:a,onChange:function(e){return b({busDetailsSectionTitle:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.TextControl,{label:Object(c.__)("Custom Fields Section Title"),value:i,onChange:function(e){return b({customFieldsSectionTitle:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.ToggleControl,{label:Object(c.__)("Add the description field to the form"),checked:u,onChange:function(e){return b({addDescriptionField:e})}})),Object(n.createElement)(r.PanelRow,null,Object(n.createElement)(r.ToggleControl,{label:Object(c.__)("Add logo upload option to the form"),checked:s,onChange:function(e){return b({addLogoUpload:e})}})),Object(n.createElement)(r.PanelRow,null,h)));return[Object(n.createElement)("div",{className:e.className},m,Object(n.createElement)("div",{className:"membership_form"},"This block adds the Chamber Dashboard membership or renewal form to your page."))]};Object(p.registerBlockType)("cdash-bd-blocks/membership-form",{title:"Membership Form",icon:"list-view",category:"cd-blocks",description:Object(c.__)("The Membership Form block displays the membership form to add new members.","cdashmm"),example:{},attributes:{action:{type:"string",default:"signup"},customFieldsArray:{type:"array",default:[]},busDetailsSectionTitle:{type:"string",default:Object(c.__)("Business Details","cdashmm")},customFieldsSectionTitle:{type:"string",default:Object(c.__)("Custom Fields","cdashmm")},addDescriptionField:{type:"boolean",default:!1},addLogoUpload:{type:"boolean",default:!1}},edit:P,save:function(){return null}});wp.compose.withState;var w=function(e){e.attributes,e.setAttributes;return[Object(n.createElement)("div",{className:e.className},Object(n.createElement)("div",{className:"member_login_form"},"This block adds a member login form and member account info to your page."))]};Object(p.registerBlockType)("cdash-bd-blocks/login-form",{title:"Member Login Form",icon:"list-view",category:"cd-blocks",description:Object(c.__)("The Member Login Form block displays the login form.","cdashmm"),example:{},edit:w,save:function(){return null}})}]);