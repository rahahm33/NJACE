﻿
(function(){CKEDITOR.plugins.add('sourcearea',{lang:'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn',icons:'source,source-rtl',hidpi:true,init:function(editor){CKEDITOR.document.appendStyleText('.textareackcontnet { overflow-y:auto}');if(editor.elementMode==CKEDITOR.ELEMENT_MODE_INLINE)
return;var sourcearea=CKEDITOR.plugins.sourcearea;editor.addMode('source',function(callback){var contentsSpace=editor.ui.space('contents'),textarea=contentsSpace.getDocument().createElement('textarea');textarea.setStyles(CKEDITOR.tools.extend({width:CKEDITOR.env.ie7Compat?'99%':'100%',height:'100%',resize:'none',outline:'none','box-sizing':'border-box','padding':'20px',color:'#333','text-align':(editor.config.direction=='ltr')?'left':'right'},CKEDITOR.tools.cssVendorPrefix('tab-size',editor.config.sourceAreaTabSize||4)));textarea.setAttribute('dir',editor.config.direction);textarea.setAttribute('placeholder',editor.config.placeholder);textarea.addClass('cke_source cke_reset cke_enable_context_menu');editor.ui.space('contents').addClass('textareackcontnet');editor.ui.space('contents').append(textarea);var editable=editor.editable(new sourceEditable(editor,textarea));editable.setData(editor.getData(1));if(CKEDITOR.env.ie){editable.attachListener(editor,'resize',onResize,editable);editable.attachListener(CKEDITOR.document.getWindow(),'resize',onResize,editable);CKEDITOR.tools.setTimeout(onResize,0,editable);}
editor.fire('ariaWidget',this);callback();});editor.addCommand('source',sourcearea.commands.source);if(editor.ui.addButton){editor.ui.addButton('Source',{label:editor.lang.sourcearea.toolbar,command:'source',toolbar:'mode,10'});}
editor.on('mode',function(){editor.getCommand('source').setState(editor.mode=='source'?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF);if(editor.mode=='source'){editor.getCommand('bold').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('italic').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('underline').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('strike').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('justifyleft').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('justifyright').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('justifycenter').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('justifyblock').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('numberedlist').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('bulletedlist').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('blockquote').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('bidirtl').setState(CKEDITOR.TRISTATE_OFF);editor.getCommand('bidiltr').setState(CKEDITOR.TRISTATE_OFF);}else{editor.ui.space('contents').removeClass('textareackcontnet');}});function onResize(){this.hide();this.setStyle('height',this.getParent().$.clientHeight+'px');this.setStyle('width',this.getParent().$.clientWidth+'px');this.show();}}});var sourceEditable=CKEDITOR.tools.createClass({base:CKEDITOR.editable,proto:{setData:function(data){data=bbcodeParser.htmlToBBCode(data);this.setValue(data);this.editor.fire('dataReady');},getData:function(){return this.getValue();},insertHtml:function(){},insertElement:function(){},insertText:function(){},setReadOnly:function(isReadOnly){this[(isReadOnly?'set':'remove')+'Attribute']('readOnly','readonly');},detach:function(){sourceEditable.baseProto.detach.call(this);this.clearCustomData();this.remove();}}});})();CKEDITOR.plugins.sourcearea={commands:{source:{modes:{wysiwyg:1,source:1},editorFocus:false,readOnly:1,exec:function(editor){if(editor.mode=='wysiwyg')
editor.fire('saveSnapshot');editor.getCommand('source').setState(CKEDITOR.TRISTATE_DISABLED);editor.setMode(editor.mode=='source'?'wysiwyg':'source');},canUndo:false}}};