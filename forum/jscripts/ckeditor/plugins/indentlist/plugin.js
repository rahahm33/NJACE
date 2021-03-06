﻿
(function(){'use strict';var isNotWhitespaces=CKEDITOR.dom.walker.whitespaces(true),isNotBookmark=CKEDITOR.dom.walker.bookmark(false,true),TRISTATE_DISABLED=CKEDITOR.TRISTATE_DISABLED,TRISTATE_OFF=CKEDITOR.TRISTATE_OFF;CKEDITOR.plugins.add('indentlist',{requires:'indent',init:function(editor){var globalHelpers=CKEDITOR.plugins.indent,editable=editor;globalHelpers.registerCommands(editor,{indentlist:new commandDefinition(editor,'indentlist',true),outdentlist:new commandDefinition(editor,'outdentlist')});function commandDefinition(editor,name){globalHelpers.specificDefinition.apply(this,arguments);this.requiredContent=['ul','ol'];editor.on('key',function(evt){if(editor.mode!='wysiwyg')
return;if(evt.data.keyCode==this.indentKey){var list=this.getContext(editor.elementPath());if(list){if(this.isIndent&&firstItemInPath.call(this,editor.elementPath(),list))
return;editor.execCommand(this.relatedGlobal);evt.cancel();}}},this);this.jobs[this.isIndent?10:30]={refresh:this.isIndent?function(editor,path){var list=this.getContext(path),inFirstListItem=firstItemInPath.call(this,path,list);if(!list||!this.isIndent||inFirstListItem)
return TRISTATE_DISABLED;return TRISTATE_OFF;}:function(editor,path){var list=this.getContext(path);if(!list||this.isIndent)
return TRISTATE_DISABLED;return TRISTATE_OFF;},exec:CKEDITOR.tools.bind(indentList,this)};}
CKEDITOR.tools.extend(commandDefinition.prototype,globalHelpers.specificDefinition.prototype,{context:{ol:1,ul:1}});}});function indentList(editor){var that=this,database=this.database,context=this.context;function indent(listNode){var startContainer=range.startContainer,endContainer=range.endContainer;while(startContainer&&!startContainer.getParent().equals(listNode))
startContainer=startContainer.getParent();while(endContainer&&!endContainer.getParent().equals(listNode))
endContainer=endContainer.getParent();if(!startContainer||!endContainer)
return false;var block=startContainer,itemsToMove=[],stopFlag=false;while(!stopFlag){if(block.equals(endContainer))
stopFlag=true;itemsToMove.push(block);block=block.getNext();}
if(itemsToMove.length<1)
return false;var listParents=listNode.getParents(true);for(var i=0;i<listParents.length;i++){if(listParents[i].getName&&context[listParents[i].getName()]){listNode=listParents[i];break;}}
var indentOffset=that.isIndent?1:-1,startItem=itemsToMove[0],lastItem=itemsToMove[itemsToMove.length-1],listArray=CKEDITOR.plugins.list.listToArray(listNode,database),baseIndent=listArray[lastItem.getCustomData('listarray_index')].indent;for(i=startItem.getCustomData('listarray_index');i<=lastItem.getCustomData('listarray_index');i++){listArray[i].indent+=indentOffset;if(indentOffset>0){var listRoot=listArray[i].parent;listArray[i].parent=new CKEDITOR.dom.element(listRoot.getName(),listRoot.getDocument());}}
for(i=lastItem.getCustomData('listarray_index')+1;i<listArray.length&&listArray[i].indent>baseIndent;i++)
listArray[i].indent+=indentOffset;var newList=CKEDITOR.plugins.list.arrayToList(listArray,database,null,editor.config.enterMode,listNode.getDirection());if(!that.isIndent){var parentLiElement;if((parentLiElement=listNode.getParent())&&parentLiElement.is('li')){var children=newList.listNode.getChildren(),pendingLis=[],count=children.count(),child;for(i=count-1;i>=0;i--){if((child=children.getItem(i))&&child.is&&child.is('li'))
pendingLis.push(child);}}}
if(newList)
newList.listNode.replace(listNode);if(pendingLis&&pendingLis.length){for(i=0;i<pendingLis.length;i++){var li=pendingLis[i],followingList=li;while((followingList=followingList.getNext())&&followingList.is&&followingList.getName()in context){if(CKEDITOR.env.needsNbspFiller&&!li.getFirst(neitherWhitespacesNorBookmark))
li.append(range.document.createText('\u00a0'));li.append(followingList);}
li.insertAfter(parentLiElement);}}
if(newList)
editor.fire('contentDomInvalidated');return true;}
var selection=editor.getSelection(),ranges=selection&&selection.getRanges(),iterator=ranges.createIterator(),range;while((range=iterator.getNextRange())){var rangeRoot=range.getCommonAncestor(),nearestListBlock=rangeRoot;while(nearestListBlock&&!(nearestListBlock.type==CKEDITOR.NODE_ELEMENT&&context[nearestListBlock.getName()]))
nearestListBlock=nearestListBlock.getParent();if(!nearestListBlock){if((nearestListBlock=range.startPath().contains(context)))
range.setEndAt(nearestListBlock,CKEDITOR.POSITION_BEFORE_END);}
if(!nearestListBlock){var selectedNode=range.getEnclosedNode();if(selectedNode&&selectedNode.type==CKEDITOR.NODE_ELEMENT&&selectedNode.getName()in context){range.setStartAt(selectedNode,CKEDITOR.POSITION_AFTER_START);range.setEndAt(selectedNode,CKEDITOR.POSITION_BEFORE_END);nearestListBlock=selectedNode;}}
if(nearestListBlock&&range.startContainer.type==CKEDITOR.NODE_ELEMENT&&range.startContainer.getName()in context){var walker=new CKEDITOR.dom.walker(range);walker.evaluator=listItem;range.startContainer=walker.next();}
if(nearestListBlock&&range.endContainer.type==CKEDITOR.NODE_ELEMENT&&range.endContainer.getName()in context){walker=new CKEDITOR.dom.walker(range);walker.evaluator=listItem;range.endContainer=walker.previous();}
if(nearestListBlock)
return indent(nearestListBlock);}
return 0;}
function firstItemInPath(path,list){if(!list)
list=path.contains(this.context);return list&&path.block&&path.block.equals(list.getFirst(listItem));}
function listItem(node){return node.type==CKEDITOR.NODE_ELEMENT&&node.is('li');}
function neitherWhitespacesNorBookmark(node){return isNotWhitespaces(node)&&isNotBookmark(node);}})();