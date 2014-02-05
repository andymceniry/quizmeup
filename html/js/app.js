/*global $*/

var AM = window.AM || {};

//var item = {};
//item.Q = 'What is the capital of Spain?';
//item.A = 'Madrid';
//localStorage.setItem('question-13567', JSON.stringify(item));


(function () {

	'use strict';

	AM.history = [];
	AM.history.push('home');

	AM.eventHandlerForButtons = (function () {
        $(function () {
			$('body').on('click', '.button', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (funcRef !== undefined) { console.log('running function: ' + funcRef);
					AM.runFunc(funcRef, btn);
				}
				if (pageRef !== undefined) { console.log('jumping to page: ' + pageRef);
					AM.showPage(pageRef, btn);
				}
				return false;
			});
        });
    }());

	AM.eventHandlerForJsLinks = (function () {
        $(function () {
			$('body').on('click', '.jslink', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (funcRef !== 'undefined') { console.log('running function: ' + funcRef);
					AM.runFunc(funcRef, btn);
				}
				if (pageRef !== 'undefined') { console.log('jumping to page: ' + pageRef);
					AM.showPage(pageRef, btn);
				}
				return false;
			});
        });
    }());


	AM.showPage = function (pageRef, el) {
		AM.history.push(pageRef);
		console.log(AM.history);
		$('.page').hide();
		$('#page-' + pageRef).show();
		AM.runPageLoadFunction(pageRef, el);
	};

	AM.runFunc = function (funcRef, el) {
		AM[funcRef]();
	};


	AM.runPageLoadFunction = function (pageRef, el) {
		switch(pageRef) {
			case 'home':
				AM.prepareTest();
				break;
			case 'quiz-question':
				console.log(AM.currentQuestion+' of '+ AM.totalQuestions);
				if (AM.currentQuestion < AM.totalQuestions) {
					AM.displayQuestion(AM.currentQuestion);
				} else {
					AM.showPage('quiz-done');
				}
				break;
		}
	};

	AM.questionCreate = function (el) {
		alert('yeah baby');
	};

	AM.prepareTest = function() {
		AM.questions = [];
		AM.questionIDs = [];
		for(var i=0;i<localStorage.length;i++) {
			var qid = parseInt(localStorage.key(i).split('question-').join(''), 10);
			AM.questions[qid] = JSON.parse(localStorage.getItem(localStorage.key(i)));
			AM.questionIDs[i] = qid;
		}
		console.log('IDs', AM.questionIDs);
		AM.questionIDs.sort(function(a,b){return a-b});
		console.log('IDs', AM.questionIDs);
		AM.currentQuestion = 0;
		AM.totalQuestions = i;
		console.log(AM.questions);
	};

	
	AM.displayQuestion = function(id) {
		var QA = AM.questions[AM.questionIDs[id]];
		$('.quiz-question-question-holder').html(QA.Q);
		$('.quiz-question-answer-holder').html(QA.A);	
	};
	
	AM.questionAnsweredCorrectly = function() {
		var oldID = AM.questionIDs[AM.currentQuestion],
			QA = AM.questions[oldID],
			level = AM.getLevelFromID(oldID),
			newID = (10000000000000*(level+1)) + (new Date).getTime();
		localStorage.removeItem('question-'+oldID);
		AM.addQuestion(newID, QA.Q, QA.A);
		console.log('correct', newID );
		AM.currentQuestion++;
	};
	
	AM.questionAnsweredIncorrectly = function() {
		var oldID = AM.questionIDs[AM.currentQuestion],
			QA = AM.questions[oldID],
			newID = (new Date).getTime();
		localStorage.removeItem('question-'+oldID);
		AM.addQuestion(newID, QA.Q, QA.A);
		console.log('incorrect', AM.getLevelFromID(oldID));
		AM.currentQuestion++;
	};
	
	AM.prepareTest();
	
	AM.addQuestion = function(id, question, answer) {
		var item = {};
		item.Q = question;
		item.A = answer;
		localStorage.setItem('question-'+id, JSON.stringify(item));
	};
	
	AM.getLevelFromID = function(id) {
		if (id < 10000000000000) {
			return 0;
		}
		if (id < 100000000000000) {
			return 1;
		}
	}
	
}());
