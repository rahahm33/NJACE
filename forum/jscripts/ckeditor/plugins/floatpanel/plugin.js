﻿
CKEDITOR.plugins.add('floatpanel',{requires:'panel'});(function(){var panels={};function getPanel(editor,doc,parentElement,definition,level){var key=CKEDITOR.tools.genKey(doc.getUniqueId(),parentElement.getUniqueId(),editor.lang.dir,editor.uiColor||'',definition.css||'',level||''),panel=panels[key];if(!panel){panel=panels[key]=new CKEDITOR.ui.panel(doc,definition);panel.element=parentElement.append(CKEDITOR.dom.element.createFromHtml(panel.render(editor),doc));panel.element.setStyles({display:'none',position:'absolute'});}
return panel;}
CKEDITOR.ui.floatPanel=CKEDITOR.tools.createClass({$:function(editor,parentElement,definition,level){definition.forceIFrame=1;if(definition.toolbarRelated&&editor.elementMode==CKEDITOR.ELEMENT_MODE_INLINE)
parentElement=CKEDITOR.document.getById('cke_'+editor.name);var doc=parentElement.getDocument(),panel=getPanel(editor,doc,parentElement,definition,level||0),element=panel.element,iframe=element.getFirst(),that=this;element.disableContextMenu();this.element=element;this._={editor:editor,panel:panel,parentElement:parentElement,definition:definition,document:doc,iframe:iframe,children:[],dir:editor.lang.dir};editor.on('mode',hide);editor.on('resize',hide);doc.getWindow().on('resize',hide);function hide(){that.hide();}},proto:{addBlock:function(name,block){return this._.panel.addBlock(name,block);},addListBlock:function(name,multiSelect){return this._.panel.addListBlock(name,multiSelect);},getBlock:function(name){return this._.panel.getBlock(name);},showBlock:function(name,offsetParent,corner,offsetX,offsetY,callback){var panel=this._.panel,block=panel.showBlock(name);this.allowBlur(false);var editable=this._.editor.editable();this._.returnFocus=editable.hasFocus?editable:new CKEDITOR.dom.element(CKEDITOR.document.$.activeElement);var element=this.element,iframe=this._.iframe,focused=CKEDITOR.env.ie?iframe:new CKEDITOR.dom.window(iframe.$.contentWindow),doc=element.getDocument(),positionedAncestor=this._.parentElement.getPositionedAncestor(),position=offsetParent.getDocumentPosition(doc),positionedAncestorPosition=positionedAncestor?positionedAncestor.getDocumentPosition(doc):{x:0,y:0},rtl=this._.dir=='rtl',left=position.x+(offsetX||0)-positionedAncestorPosition.x,top=position.y+(offsetY||0)-positionedAncestorPosition.y;if(rtl&&(corner==1||corner==4))
left+=offsetParent.$.offsetWidth;else if(!rtl&&(corner==2||corner==3))
left+=offsetParent.$.offsetWidth-1;if(corner==3||corner==4)
top+=offsetParent.$.offsetHeight-1;this._.panel._.offsetParentId=offsetParent.getId();element.setStyles({top:top+'px',left:0,display:''});element.setOpacity(0);element.getFirst().removeStyle('width');this._.editor.focusManager.add(focused);if(!this._.blurSet){CKEDITOR.event.useCapture=true;focused.on('blur',function(ev){if(!this.allowBlur()||ev.data.getPhase()!=CKEDITOR.EVENT_PHASE_AT_TARGET)
return;if(this.visible&&!this._.activeChild){delete this._.returnFocus;this.hide();}},this);focused.on('focus',function(){this._.focused=true;this.hideChild();this.allowBlur(true);},this);CKEDITOR.event.useCapture=false;this._.blurSet=1;}
panel.onEscape=CKEDITOR.tools.bind(function(keystroke){if(this.onEscape&&this.onEscape(keystroke)===false)
return false;},this);CKEDITOR.tools.setTimeout(function(){var panelLoad=CKEDITOR.tools.bind(function(){var target=element;target.removeStyle('width');if(block.autoSize){var panelDoc=block.element.getDocument();var width=(CKEDITOR.env.webkit?block.element:panelDoc.getBody())['$'].scrollWidth;if(CKEDITOR.env.ie&&CKEDITOR.env.quirks&&width>0)
width+=(target.$.offsetWidth||0)-(target.$.clientWidth||0)+3;width+=10;target.setStyle('width',width+'px');var height=block.element.$.scrollHeight;if(CKEDITOR.env.ie&&CKEDITOR.env.quirks&&height>0)
height+=(target.$.offsetHeight||0)-(target.$.clientHeight||0)+3;target.setStyle('height',height+'px');panel._.currentBlock.element.setStyle('display','none').removeStyle('display');}else
target.removeStyle('height');if(rtl)
left-=element.$.offsetWidth;element.setStyle('left',left+'px');var panelElement=panel.element,panelWindow=panelElement.getWindow(),rect=element.$.getBoundingClientRect(),viewportSize=panelWindow.getViewPaneSize();var rectWidth=rect.width||rect.right-rect.left,rectHeight=rect.height||rect.bottom-rect.top;var spaceAfter=rtl?rect.right:viewportSize.width-rect.left,spaceBefore=rtl?viewportSize.width-rect.right:rect.left;if(rtl){if(spaceAfter<rectWidth){if(spaceBefore>rectWidth)
left+=rectWidth;else if(viewportSize.width>rectWidth)
left=left-rect.left;else
left=left-rect.right+viewportSize.width;}}else if(spaceAfter<rectWidth){if(spaceBefore>rectWidth)
left-=rectWidth;else if(viewportSize.width>rectWidth)
left=left-rect.right+viewportSize.width;else
left=left-rect.left;}
var spaceBelow=viewportSize.height-rect.top,spaceAbove=rect.top;if(spaceBelow<rectHeight){if(spaceAbove>rectHeight)
top-=rectHeight;else if(viewportSize.height>rectHeight)
top=top-rect.bottom+viewportSize.height;else
top=top-rect.top;}
if(CKEDITOR.env.ie){var offsetParent=new CKEDITOR.dom.element(element.$.offsetParent),scrollParent=offsetParent;if(scrollParent.getName()=='html')
scrollParent=scrollParent.getDocument().getBody();if(scrollParent.getComputedStyle('direction')=='rtl'){if(CKEDITOR.env.ie8Compat)
left-=element.getDocument().getDocumentElement().$.scrollLeft*2;else
left-=(offsetParent.$.scrollWidth-offsetParent.$.clientWidth);}}
var innerElement=element.getFirst(),activePanel;if((activePanel=innerElement.getCustomData('activePanel')))
activePanel.onHide&&activePanel.onHide.call(this,1);innerElement.setCustomData('activePanel',this);element.setStyles({top:top+'px',left:left+'px'});element.setOpacity(1);callback&&callback();},this);panel.isLoaded?panelLoad():panel.onLoad=panelLoad;CKEDITOR.tools.setTimeout(function(){var scrollTop=CKEDITOR.env.webkit&&CKEDITOR.document.getWindow().getScrollPosition().y;this.focus();block.element.focus();if(CKEDITOR.env.webkit)
CKEDITOR.document.getBody().$.scrollTop=scrollTop;this.allowBlur(true);this._.editor.fire('panelShow',this);},0,this);},CKEDITOR.env.air?200:0,this);this.visible=1;if(this.onShow)
this.onShow.call(this);},focus:function(){if(CKEDITOR.env.webkit){var active=CKEDITOR.document.getActive();!active.equals(this._.iframe)&&active.$.blur();}
var focus=this._.lastFocused||this._.iframe.getFrameDocument().getWindow();focus.focus();},blur:function(){var doc=this._.iframe.getFrameDocument(),active=doc.getActive();active.is('a')&&(this._.lastFocused=active);},hide:function(returnFocus){if(this.visible&&(!this.onHide||this.onHide.call(this)!==true)){this.hideChild();CKEDITOR.env.gecko&&this._.iframe.getFrameDocument().$.activeElement.blur();this.element.setStyle('display','none');this.visible=0;this.element.getFirst().removeCustomData('activePanel');var focusReturn=returnFocus&&this._.returnFocus;if(focusReturn){if(CKEDITOR.env.webkit&&focusReturn.type)
focusReturn.getWindow().$.focus();focusReturn.focus();}
delete this._.lastFocused;this._.editor.fire('panelHide',this);}},allowBlur:function(allow)
{var panel=this._.panel;if(allow!=undefined)
panel.allowBlur=allow;return panel.allowBlur;},showAsChild:function(panel,blockName,offsetParent,corner,offsetX,offsetY){if(this._.activeChild==panel&&panel._.panel._.offsetParentId==offsetParent.getId())
return;this.hideChild();panel.onHide=CKEDITOR.tools.bind(function(){CKEDITOR.tools.setTimeout(function(){if(!this._.focused)
this.hide();},0,this);},this);this._.activeChild=panel;this._.focused=false;panel.showBlock(blockName,offsetParent,corner,offsetX,offsetY);this.blur();if(CKEDITOR.env.ie7Compat||CKEDITOR.env.ie6Compat){setTimeout(function(){panel.element.getChild(0).$.style.cssText+='';},100);}},hideChild:function(restoreFocus){var activeChild=this._.activeChild;if(activeChild){delete activeChild.onHide;delete this._.activeChild;activeChild.hide();restoreFocus&&this.focus();}}}});CKEDITOR.on('instanceDestroyed',function(){var isLastInstance=CKEDITOR.tools.isEmpty(CKEDITOR.instances);for(var i in panels){var panel=panels[i];if(isLastInstance)
panel.destroy();else
panel.element.hide();}
isLastInstance&&(panels={});});})();