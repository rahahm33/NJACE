﻿
(function(){var meta={editorFocus:false,modes:{wysiwyg:1,source:1}};var blurCommand={exec:function(editor){editor.container.focusNext(true,editor.tabIndex);}};var blurBackCommand={exec:function(editor){editor.container.focusPrevious(true,editor.tabIndex);}};function selectNextCellCommand(backward){return{editorFocus:false,canUndo:false,modes:{wysiwyg:1},exec:function(editor){if(editor.editable().hasFocus){var sel=editor.getSelection(),path=new CKEDITOR.dom.elementPath(sel.getCommonAncestor(),sel.root),cell;if((cell=path.contains({td:1,th:1},1))){var resultRange=editor.createRange(),next=CKEDITOR.tools.tryThese(function(){var row=cell.getParent(),next=row.$.cells[cell.$.cellIndex+(backward?-1:1)];next.parentNode.parentNode;return next;},function(){var row=cell.getParent(),table=row.getAscendant('table'),nextRow=table.$.rows[row.$.rowIndex+(backward?-1:1)];return nextRow.cells[backward?nextRow.cells.length-1:0];});if(!(next||backward)){var table=cell.getAscendant('table').$,cells=cell.getParent().$.cells;var newRow=new CKEDITOR.dom.element(table.insertRow(-1),editor.document);for(var i=0,count=cells.length;i<count;i++){var newCell=newRow.append(new CKEDITOR.dom.element(cells[i],editor.document).clone(false,false));newCell.appendBogus();}
resultRange.moveToElementEditStart(newRow);}else if(next){next=new CKEDITOR.dom.element(next);resultRange.moveToElementEditStart(next);if(!(resultRange.checkStartOfBlock()&&resultRange.checkEndOfBlock()))
resultRange.selectNodeContents(next);}else
return true;resultRange.select(true);return true;}}
return false;}};}
CKEDITOR.plugins.add('tab',{init:function(editor){var tabTools=editor.config.enableTabKeyTools!==false,tabSpaces=editor.config.tabSpaces||0,tabText='';while(tabSpaces--)
tabText+='\xa0';if(tabText){editor.on('key',function(ev){if(ev.data.keyCode==9)
{editor.insertHtml(tabText);ev.cancel();}});}
if(tabTools){editor.on('key',function(ev){if(ev.data.keyCode==9&&editor.execCommand('selectNextCell')||ev.data.keyCode==(CKEDITOR.SHIFT+9)&&editor.execCommand('selectPreviousCell'))
ev.cancel();});}
editor.addCommand('blur',CKEDITOR.tools.extend(blurCommand,meta));editor.addCommand('blurBack',CKEDITOR.tools.extend(blurBackCommand,meta));editor.addCommand('selectNextCell',selectNextCellCommand());editor.addCommand('selectPreviousCell',selectNextCellCommand(true));}});})();CKEDITOR.dom.element.prototype.focusNext=function(ignoreChildren,indexToUse){var $=this.$,curTabIndex=(indexToUse===undefined?this.getTabIndex():indexToUse),passedCurrent,enteredCurrent,elected,electedTabIndex,element,elementTabIndex;if(curTabIndex<=0){element=this.getNextSourceNode(ignoreChildren,CKEDITOR.NODE_ELEMENT);while(element){if(element.isVisible()&&element.getTabIndex()===0){elected=element;break;}
element=element.getNextSourceNode(false,CKEDITOR.NODE_ELEMENT);}}else{element=this.getDocument().getBody().getFirst();while((element=element.getNextSourceNode(false,CKEDITOR.NODE_ELEMENT))){if(!passedCurrent){if(!enteredCurrent&&element.equals(this)){enteredCurrent=true;if(ignoreChildren){if(!(element=element.getNextSourceNode(true,CKEDITOR.NODE_ELEMENT)))
break;passedCurrent=1;}}else if(enteredCurrent&&!this.contains(element))
passedCurrent=1;}
if(!element.isVisible()||(elementTabIndex=element.getTabIndex())<0)
continue;if(passedCurrent&&elementTabIndex==curTabIndex){elected=element;break;}
if(elementTabIndex>curTabIndex&&(!elected||!electedTabIndex||elementTabIndex<electedTabIndex)){elected=element;electedTabIndex=elementTabIndex;}else if(!elected&&elementTabIndex===0){elected=element;electedTabIndex=elementTabIndex;}}}
if(elected)
elected.focus();};CKEDITOR.dom.element.prototype.focusPrevious=function(ignoreChildren,indexToUse){var $=this.$,curTabIndex=(indexToUse===undefined?this.getTabIndex():indexToUse),passedCurrent,enteredCurrent,elected,electedTabIndex=0,elementTabIndex;var element=this.getDocument().getBody().getLast();while((element=element.getPreviousSourceNode(false,CKEDITOR.NODE_ELEMENT))){if(!passedCurrent){if(!enteredCurrent&&element.equals(this)){enteredCurrent=true;if(ignoreChildren){if(!(element=element.getPreviousSourceNode(true,CKEDITOR.NODE_ELEMENT)))
break;passedCurrent=1;}}else if(enteredCurrent&&!this.contains(element))
passedCurrent=1;}
if(!element.isVisible()||(elementTabIndex=element.getTabIndex())<0)
continue;if(curTabIndex<=0){if(passedCurrent&&elementTabIndex===0){elected=element;break;}
if(elementTabIndex>electedTabIndex){elected=element;electedTabIndex=elementTabIndex;}}else{if(passedCurrent&&elementTabIndex==curTabIndex){elected=element;break;}
if(elementTabIndex<curTabIndex&&(!elected||elementTabIndex>electedTabIndex)){elected=element;electedTabIndex=elementTabIndex;}}}
if(elected)
elected.focus();};