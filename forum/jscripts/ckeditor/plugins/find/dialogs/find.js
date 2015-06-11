﻿
(function(){var isReplace;function findEvaluator(node){return node.type==CKEDITOR.NODE_TEXT&&node.getLength()>0&&(!isReplace||!node.isReadOnly());}
function nonCharactersBoundary(node){return!(node.type==CKEDITOR.NODE_ELEMENT&&node.isBlockBoundary(CKEDITOR.tools.extend({},CKEDITOR.dtd.$empty,CKEDITOR.dtd.$nonEditable)));}
var cursorStep=function(){return{textNode:this.textNode,offset:this.offset,character:this.textNode?this.textNode.getText().charAt(this.offset):null,hitMatchBoundary:this._.matchBoundary};};var pages=['find','replace'],fieldsMapping=[['txtFindFind','txtFindReplace'],['txtFindCaseChk','txtReplaceCaseChk'],['txtFindWordChk','txtReplaceWordChk'],['txtFindCyclic','txtReplaceCyclic']];function syncFieldsBetweenTabs(currentPageId){var sourceIndex,targetIndex,sourceField,targetField;sourceIndex=currentPageId==='find'?1:0;targetIndex=1-sourceIndex;var i,l=fieldsMapping.length;for(i=0;i<l;i++){sourceField=this.getContentElement(pages[sourceIndex],fieldsMapping[i][sourceIndex]);targetField=this.getContentElement(pages[targetIndex],fieldsMapping[i][targetIndex]);targetField.setValue(sourceField.getValue());}}
var findDialog=function(editor,startupPage){var highlightStyle=new CKEDITOR.style(CKEDITOR.tools.extend({attributes:{'data-cke-highlight':1},fullMatch:1,ignoreReadonly:1,childRule:function(){return 0;}},editor.config.find_highlight,true));var characterWalker=function(range,matchWord){var self=this;var walker=new CKEDITOR.dom.walker(range);walker.guard=matchWord?nonCharactersBoundary:function(node){!nonCharactersBoundary(node)&&(self._.matchBoundary=true);};walker['evaluator']=findEvaluator;walker.breakOnFalse=1;if(range.startContainer.type==CKEDITOR.NODE_TEXT){this.textNode=range.startContainer;this.offset=range.startOffset-1;}
this._={matchWord:matchWord,walker:walker,matchBoundary:false};};characterWalker.prototype={next:function(){return this.move();},back:function(){return this.move(true);},move:function(rtl){var currentTextNode=this.textNode;if(currentTextNode===null)
return cursorStep.call(this);this._.matchBoundary=false;if(currentTextNode&&rtl&&this.offset>0){this.offset--;return cursorStep.call(this);}else if(currentTextNode&&this.offset<currentTextNode.getLength()-1){this.offset++;return cursorStep.call(this);}else{currentTextNode=null;while(!currentTextNode){currentTextNode=this._.walker[rtl?'previous':'next'].call(this._.walker);if(this._.matchWord&&!currentTextNode||this._.walker._.end)
break;}
this.textNode=currentTextNode;if(currentTextNode)
this.offset=rtl?currentTextNode.getLength()-1:0;else
this.offset=0;}
return cursorStep.call(this);}};var characterRange=function(characterWalker,rangeLength){this._={walker:characterWalker,cursors:[],rangeLength:rangeLength,highlightRange:null,isMatched:0};};characterRange.prototype={toDomRange:function(){var range=editor.createRange();var cursors=this._.cursors;if(cursors.length<1){var textNode=this._.walker.textNode;if(textNode)
range.setStartAfter(textNode);else
return null;}else{var first=cursors[0],last=cursors[cursors.length-1];range.setStart(first.textNode,first.offset);range.setEnd(last.textNode,last.offset+1);}
return range;},updateFromDomRange:function(domRange){var cursor,walker=new characterWalker(domRange);this._.cursors=[];do{cursor=walker.next();if(cursor.character)this._.cursors.push(cursor);}
while(cursor.character);this._.rangeLength=this._.cursors.length;},setMatched:function(){this._.isMatched=true;},clearMatched:function(){this._.isMatched=false;},isMatched:function(){return this._.isMatched;},highlight:function(){if(this._.cursors.length<1)
return;if(this._.highlightRange)
this.removeHighlight();var range=this.toDomRange(),bookmark=range.createBookmark();highlightStyle.applyToRange(range);range.moveToBookmark(bookmark);this._.highlightRange=range;var element=range.startContainer;if(element.type!=CKEDITOR.NODE_ELEMENT)
element=element.getParent();element.scrollIntoView();this.updateFromDomRange(range);},removeHighlight:function(){if(!this._.highlightRange)
return;var bookmark=this._.highlightRange.createBookmark();highlightStyle.removeFromRange(this._.highlightRange);this._.highlightRange.moveToBookmark(bookmark);this.updateFromDomRange(this._.highlightRange);this._.highlightRange=null;},isReadOnly:function(){if(!this._.highlightRange)
return 0;return this._.highlightRange.startContainer.isReadOnly();},moveBack:function(){var retval=this._.walker.back(),cursors=this._.cursors;if(retval.hitMatchBoundary)
this._.cursors=cursors=[];cursors.unshift(retval);if(cursors.length>this._.rangeLength)
cursors.pop();return retval;},moveNext:function(){var retval=this._.walker.next(),cursors=this._.cursors;if(retval.hitMatchBoundary)
this._.cursors=cursors=[];cursors.push(retval);if(cursors.length>this._.rangeLength)
cursors.shift();return retval;},getEndCharacter:function(){var cursors=this._.cursors;if(cursors.length<1)
return null;return cursors[cursors.length-1].character;},getNextCharacterRange:function(maxLength){var lastCursor,nextRangeWalker,cursors=this._.cursors;if((lastCursor=cursors[cursors.length-1])&&lastCursor.textNode)
nextRangeWalker=new characterWalker(getRangeAfterCursor(lastCursor));else
nextRangeWalker=this._.walker;return new characterRange(nextRangeWalker,maxLength);},getCursors:function(){return this._.cursors;}};function getRangeAfterCursor(cursor,inclusive){var range=editor.createRange();range.setStart(cursor.textNode,(inclusive?cursor.offset:cursor.offset+1));range.setEndAt(editor.editable(),CKEDITOR.POSITION_BEFORE_END);return range;}
function getRangeBeforeCursor(cursor){var range=editor.createRange();range.setStartAt(editor.editable(),CKEDITOR.POSITION_AFTER_START);range.setEnd(cursor.textNode,cursor.offset);return range;}
var KMP_NOMATCH=0,KMP_ADVANCED=1,KMP_MATCHED=2;var kmpMatcher=function(pattern,ignoreCase){var overlap=[-1];if(ignoreCase)
pattern=pattern.toLowerCase();for(var i=0;i<pattern.length;i++){overlap.push(overlap[i]+1);while(overlap[i+1]>0&&pattern.charAt(i)!=pattern.charAt(overlap[i+1]-1))
overlap[i+1]=overlap[overlap[i+1]-1]+1;}
this._={overlap:overlap,state:0,ignoreCase:!!ignoreCase,pattern:pattern};};kmpMatcher.prototype={feedCharacter:function(c){if(this._.ignoreCase)
c=c.toLowerCase();while(true){if(c==this._.pattern.charAt(this._.state)){this._.state++;if(this._.state==this._.pattern.length){this._.state=0;return KMP_MATCHED;}
return KMP_ADVANCED;}else if(!this._.state)
return KMP_NOMATCH;else
this._.state=this._.overlap[this._.state];}
return null;},reset:function(){this._.state=0;}};var wordSeparatorRegex=/[.,"'?!;: \u0085\u00a0\u1680\u280e\u2028\u2029\u202f\u205f\u3000]/;var isWordSeparator=function(c){if(!c)
return true;var code=c.charCodeAt(0);return(code>=9&&code<=0xd)||(code>=0x2000&&code<=0x200a)||wordSeparatorRegex.test(c);};var finder={searchRange:null,matchRange:null,find:function(pattern,matchCase,matchWord,matchCyclic,highlightMatched,cyclicRerun){if(!this.matchRange)
this.matchRange=new characterRange(new characterWalker(this.searchRange),pattern.length);else{this.matchRange.removeHighlight();this.matchRange=this.matchRange.getNextCharacterRange(pattern.length);}
var matcher=new kmpMatcher(pattern,!matchCase),matchState=KMP_NOMATCH,character='%';while(character!==null){this.matchRange.moveNext();while((character=this.matchRange.getEndCharacter())){matchState=matcher.feedCharacter(character);if(matchState==KMP_MATCHED)
break;if(this.matchRange.moveNext().hitMatchBoundary)
matcher.reset();}
if(matchState==KMP_MATCHED){if(matchWord){var cursors=this.matchRange.getCursors(),tail=cursors[cursors.length-1],head=cursors[0];var rangeBefore=getRangeBeforeCursor(head),rangeAfter=getRangeAfterCursor(tail);rangeBefore.trim();rangeAfter.trim();var headWalker=new characterWalker(rangeBefore,true),tailWalker=new characterWalker(rangeAfter,true);if(!(isWordSeparator(headWalker.back().character)&&isWordSeparator(tailWalker.next().character)))
continue;}
this.matchRange.setMatched();if(highlightMatched!==false)
this.matchRange.highlight();return true;}}
this.matchRange.clearMatched();this.matchRange.removeHighlight();if(matchCyclic&&!cyclicRerun){this.searchRange=getSearchRange(1);this.matchRange=null;return arguments.callee.apply(this,Array.prototype.slice.call(arguments).concat([true]));}
return false;},replaceCounter:0,replace:function(dialog,pattern,newString,matchCase,matchWord,matchCyclic,isReplaceAll){isReplace=1;var result=0;if(this.matchRange&&this.matchRange.isMatched()&&!this.matchRange._.isReplaced&&!this.matchRange.isReadOnly()){this.matchRange.removeHighlight();var domRange=this.matchRange.toDomRange();var text=editor.document.createText(newString);if(!isReplaceAll){var selection=editor.getSelection();selection.selectRanges([domRange]);editor.fire('saveSnapshot');}
domRange.deleteContents();domRange.insertNode(text);if(!isReplaceAll){selection.selectRanges([domRange]);editor.fire('saveSnapshot');}
this.matchRange.updateFromDomRange(domRange);if(!isReplaceAll)
this.matchRange.highlight();this.matchRange._.isReplaced=true;this.replaceCounter++;result=1;}else
result=this.find(pattern,matchCase,matchWord,matchCyclic,!isReplaceAll);isReplace=0;return result;}};function getSearchRange(isDefault){var searchRange,sel=editor.getSelection(),editable=editor.editable();if(sel&&!isDefault){searchRange=sel.getRanges()[0].clone();searchRange.collapse(true);}else{searchRange=editor.createRange();searchRange.setStartAt(editable,CKEDITOR.POSITION_AFTER_START);}
searchRange.setEndAt(editable,CKEDITOR.POSITION_BEFORE_END);return searchRange;}
var lang=editor.lang.find;return{title:lang.title,resizable:CKEDITOR.DIALOG_RESIZE_NONE,minWidth:350,minHeight:170,buttons:[CKEDITOR.dialog.cancelButton(editor,{label:editor.lang.common.close})],contents:[{id:'find',label:lang.find,title:lang.find,accessKey:'',elements:[{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtFindFind',label:lang.findWhat,isChanged:false,labelLayout:'horizontal',accessKey:'F'},{type:'button',id:'btnFind',align:'left',style:'width:100%',label:lang.find,onClick:function(){var dialog=this.getDialog();if(!finder.find(dialog.getValueOf('find','txtFindFind'),dialog.getValueOf('find','txtFindCaseChk'),dialog.getValueOf('find','txtFindWordChk'),dialog.getValueOf('find','txtFindCyclic')))
alert(lang.notFoundMsg);}}]},{type:'fieldset',label:CKEDITOR.tools.htmlEncode(lang.findOptions),style:'margin-top:29px',children:[{type:'vbox',padding:0,children:[{type:'checkbox',id:'txtFindCaseChk',isChanged:false,label:lang.matchCase},{type:'checkbox',id:'txtFindWordChk',isChanged:false,label:lang.matchWord},{type:'checkbox',id:'txtFindCyclic',isChanged:false,'default':true,label:lang.matchCyclic}]}]}]},{id:'replace',label:lang.replace,accessKey:'M',elements:[{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtFindReplace',label:lang.findWhat,isChanged:false,labelLayout:'horizontal',accessKey:'F'},{type:'button',id:'btnFindReplace',align:'left',style:'width:100%',label:lang.replace,onClick:function(){var dialog=this.getDialog();if(!finder.replace(dialog,dialog.getValueOf('replace','txtFindReplace'),dialog.getValueOf('replace','txtReplace'),dialog.getValueOf('replace','txtReplaceCaseChk'),dialog.getValueOf('replace','txtReplaceWordChk'),dialog.getValueOf('replace','txtReplaceCyclic')))
alert(lang.notFoundMsg);}}]},{type:'hbox',widths:['230px','90px'],children:[{type:'text',id:'txtReplace',label:lang.replaceWith,isChanged:false,labelLayout:'horizontal',accessKey:'R'},{type:'button',id:'btnReplaceAll',align:'left',style:'width:100%',label:lang.replaceAll,isChanged:false,onClick:function(){var dialog=this.getDialog();var replaceNums;finder.replaceCounter=0;finder.searchRange=getSearchRange(1);if(finder.matchRange){finder.matchRange.removeHighlight();finder.matchRange=null;}
editor.fire('saveSnapshot');while(finder.replace(dialog,dialog.getValueOf('replace','txtFindReplace'),dialog.getValueOf('replace','txtReplace'),dialog.getValueOf('replace','txtReplaceCaseChk'),dialog.getValueOf('replace','txtReplaceWordChk'),false,true)){}
if(finder.replaceCounter){alert(lang.replaceSuccessMsg.replace(/%1/,finder.replaceCounter));editor.fire('saveSnapshot');}else
alert(lang.notFoundMsg);}}]},{type:'fieldset',label:CKEDITOR.tools.htmlEncode(lang.findOptions),children:[{type:'vbox',padding:0,children:[{type:'checkbox',id:'txtReplaceCaseChk',isChanged:false,label:lang.matchCase},{type:'checkbox',id:'txtReplaceWordChk',isChanged:false,label:lang.matchWord},{type:'checkbox',id:'txtReplaceCyclic',isChanged:false,'default':true,label:lang.matchCyclic}]}]}]}],onLoad:function(){var dialog=this;var patternField,wholeWordChkField;var isUserSelect=0;this.on('hide',function(){isUserSelect=0;});this.on('show',function(){isUserSelect=1;});this.selectPage=CKEDITOR.tools.override(this.selectPage,function(originalFunc){return function(pageId){originalFunc.call(dialog,pageId);var currPage=dialog._.tabs[pageId];var patternFieldInput,patternFieldId,wholeWordChkFieldId;patternFieldId=pageId==='find'?'txtFindFind':'txtFindReplace';wholeWordChkFieldId=pageId==='find'?'txtFindWordChk':'txtReplaceWordChk';patternField=dialog.getContentElement(pageId,patternFieldId);wholeWordChkField=dialog.getContentElement(pageId,wholeWordChkFieldId);if(!currPage.initialized){patternFieldInput=CKEDITOR.document.getById(patternField._.inputId);currPage.initialized=true;}
if(isUserSelect)
syncFieldsBetweenTabs.call(this,pageId);};});},onShow:function(){finder.searchRange=getSearchRange();var selectedText=this.getParentEditor().getSelection().getSelectedText(),patternFieldId=(startupPage=='find'?'txtFindFind':'txtFindReplace');var field=this.getContentElement(startupPage,patternFieldId);field.setValue(selectedText);field.select();this.selectPage(startupPage);this[(startupPage=='find'&&this._.editor.readOnly?'hide':'show')+'Page']('replace');},onHide:function(){var range;if(finder.matchRange&&finder.matchRange.isMatched()){finder.matchRange.removeHighlight();editor.focus();range=finder.matchRange.toDomRange();if(range)
editor.getSelection().selectRanges([range]);}
delete finder.matchRange;},onFocus:function(){if(startupPage=='replace')
return this.getContentElement('replace','txtFindReplace');else
return this.getContentElement('find','txtFindFind');}};};CKEDITOR.dialog.add('find',function(editor){return findDialog(editor,'find');});CKEDITOR.dialog.add('replace',function(editor){return findDialog(editor,'replace');});})();