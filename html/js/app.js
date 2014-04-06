/*global $*/

var AM = window.AM || {};

(function () {

	'use strict';

	AM.history = [];
	AM.history.push('home');


	AM.init = function () {
		AM.prepareTest();
		AM.moveToBottom('home');
	};

	AM.eventHandlerForButtons = (function () {
        $(function () {
			$('body').on('click', '.button', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (funcRef !== undefined) {
					AM.runFunc(funcRef, btn);
				}
				if (pageRef !== undefined) {
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
				if (funcRef !== undefined) {
					AM.runFunc(funcRef, btn);
				}
				if (pageRef !== undefined) {
					AM.showPage(pageRef, btn);
				}
				return false;
			});
        });
    }());


	AM.showPage = function (pageRef, el) {
		AM.history.push(pageRef);
		$('.page').hide();
		$('#page-' + pageRef).show();
		AM.moveToBottom(pageRef);
		AM.runPageLoadFunction(pageRef, el);
	};

	AM.runFunc = function (funcRef, el) {
		AM[funcRef]();
	};


	AM.runPageLoadFunction = function (pageRef, el) {
		switch (pageRef) {
		case 'home':
			AM.prepareTest();
			break;
		case 'quiz-question':
			if (AM.currentQuestion < AM.totalQuestions) {
				AM.displayQuestion(AM.currentQuestion);
			} else {
				AM.questionReview();
				AM.showPage('quiz-done');
			}
			break;
		case 'question-add':
			$('#question-text-add').focus();
			break;
		case 'question-search':
			$('#search-text').focus();
			break;
		}
	};

	AM.questionLevelsReset = function () {
        var i,
            qid,
            newID,
            level,
            HTML = '',
            QA,
            lsTemp = [],
            bResetConfirmed = confirm('Reset all question levels?');
		if (bResetConfirmed === true) {
            for (i = 0; i < localStorage.length; i = i + 1) {
                if (localStorage.key(i).substr(0, 9) === 'question-') {
                    qid = parseInt(localStorage.key(i).split('question-').join(''), 10);
                    level = AM.getLevelFromID(qid);
                    QA = JSON.parse(localStorage.getItem('question-' + qid));
                    if (level > 0) {
                        lsTemp[qid] = level;
                    }
                }
            }
            for (qid in lsTemp) {
                    level = lsTemp[qid];
                    QA = JSON.parse(localStorage.getItem('question-' + qid));
                    newID = qid - (level * 10000000000000);
                    AM.addQuestion(newID, QA.Q, QA.A, QA.T, QA.S);
                    localStorage.removeItem('question-' + qid);                
            }
            AM.questionReview();
        }
	};

    AM.questionReview = function () {
		var i,
			qid,
			level,
			HTML = '';
		AM.questionLevels = [];
		for (i = 0; i <= 10; i = i + 1) {
			AM.questionLevels[i] = 0;
		}
		for (i = 0; i < localStorage.length; i = i + 1) {
            if (localStorage.key(i).substr(0, 9) === 'question-') {
                qid = parseInt(localStorage.key(i).split('question-').join(''), 10);
                level = AM.getLevelFromID(qid);
                AM.questionLevels[level] = AM.questionLevels[level] + 1;
            }
		}
		for (i = 0; i <= 10; i = i + 1) {
			if (AM.questionLevels[i] > 0) {
				HTML = HTML + '<div class="quiz-review-item">';
				HTML = HTML + 'Level ' + i + ': ' + AM.questionLevels[i] + ' question';
				if (AM.questionLevels[i] !== 1) {
					HTML = HTML + 's';
				}
				HTML = HTML + '</div>';
			}
		}
		$('#quiz-review-holder').html(HTML);
	};

	AM.questionCreate = function (el) {
		var QA = {},
			newID = (new Date()).getTime() - 1000000000;
		QA.Q = $('#question-text-add').val();
		QA.A = $('#answer-text-add').val();
		if (QA.Q === '' || QA.A === '') {
			alert('Need question & answer filled in');
			return false;
		}
		AM.addQuestion(newID, QA.Q, QA.A);
        $('#question-text-add').val('');
		$('#answer-text-add').val('');
		AM.showPage('home');
	};

	AM.questionUpdate = function () {
		var QA = {};
		QA.Q = $('#question-text-edit').val();
		QA.A = $('#answer-text-edit').val();
		QA.T = $('#ts-text-edit').val();
		if (QA.Q === '' || QA.A === '') {
			alert('Need question & answer filled in');
			return false;
		}
		AM.addQuestion(AM.currentEditQID, QA.Q, QA.A, QA.T);
		AM.showPage('home');
	};

	AM.questionDelete = function () {
		var bDeleteConfirmed = confirm('Delete this question?');
		if (bDeleteConfirmed === true) {
			localStorage.removeItem('question-' + AM.currentEditQID);
			AM.showPage('question-search');
		}
		return false;
	};

	AM.processSearch = function () {
		var searchCriteria = $('#search-text').val().toLowerCase(),
			iLoop = 0,
			QA,
			Q2,
			A2,
			qid,
			HTML = '';
		if (searchCriteria === '') {
			alert('Need something to search for fool!!');
			return false;
		}
		AM.searchResults = [];
		$('#question-search-results-holder').html('');
		for (iLoop = 0; iLoop < localStorage.length; iLoop = iLoop + 1) {
            if (localStorage.key(iLoop).substr(0, 9) === 'question-') {
                QA = JSON.parse(localStorage.getItem(localStorage.key(iLoop)));
                Q2 = QA.Q.toLowerCase().split(searchCriteria);
                A2 = QA.A.toLowerCase().split(searchCriteria);
                if (Q2.length > 1 || A2.length > 1) {
                    qid = parseInt(localStorage.key(iLoop).split('question-').join(''), 10);
                    AM.searchResults.push(QA);
                    HTML = HTML + '<div class="question-search-results-item" data-qid="' + qid + '">' + QA.Q + '</div>';
                }
            }
		}
        if (HTML === '') {
            HTML = HTML + '<div class="holder"><p>No questions found macthing "' + searchCriteria + '"</p></div>';
        }
		$('#question-search-results-holder').append(HTML);
		$('.question-search-results-item').click(function () {
			var qid = $(this).data('qid'),
				q = JSON.parse(localStorage.getItem('question-' + qid));
			$('#question-text-edit').val(q.Q);
			$('#answer-text-edit').val(q.A);
			$('#ts-text-edit').val(q.T);
			AM.currentEditQID = qid;
			AM.showPage('question-edit');
		});
		AM.showPage('question-search-results');
	};

	AM.prepareTest = function () {
		var i,
            iQ = 0,
			qid,
			item = {};
		AM.questions = [];
		AM.questionIDs = [];
		for (i = 0; i < localStorage.length; i = i + 1) {
            if (localStorage.key(i).substr(0,9) == 'question-') {
                qid = parseInt(localStorage.key(i).split('question-').join(''), 10);
                AM.questions[qid] = JSON.parse(localStorage.getItem(localStorage.key(i)));
                AM.questionIDs[iQ] = qid;
                iQ = iQ + 1;
            }
		}
        if (AM.questionIDs.length < 1) {
            item.Q = 'What is the capital of Spain?';
            item.A = 'Madrid';
            localStorage.setItem('question-1', JSON.stringify(item));
            AM.prepareTest();
            return false;
        }
		AM.questionIDs.sort(function (a, b) {return a - b; });
		AM.currentQuestion = 0;
        AM.totalQuestions = AM.questionIDs.length;
	};


	AM.displayQuestion = function (id) {
		var QA = AM.questions[AM.questionIDs[id]];
        QA.Q = QA.Q.split('\n').join('<br/>');
        QA.A = QA.A.split('\n').join('<br/>');
		$('.quiz-question-question-holder').html(QA.Q);
		$('.quiz-question-answer-holder').html(QA.A);
	};

	AM.questionAnsweredCorrectly = function () {
		var oldID = AM.questionIDs[AM.currentQuestion],
			QA = AM.questions[oldID],
			level = AM.getLevelFromID(oldID),
			newID = (10000000000000 * (level + 1)) + (new Date()).getTime();
		localStorage.removeItem('question-' + oldID);
		AM.addQuestion(newID, QA.Q, QA.A, QA.T, QA.S);
		AM.currentQuestion = AM.currentQuestion + 1;
	};

	AM.questionAnsweredIncorrectly = function () {
		var oldID = AM.questionIDs[AM.currentQuestion],
			QA = AM.questions[oldID],
			newID = (new Date()).getTime();
		localStorage.removeItem('question-' + oldID);
		AM.addQuestion(newID, QA.Q, QA.A, QA.T, QA.S);
		AM.currentQuestion = AM.currentQuestion + 1;
	};

	AM.addQuestion = function (id, question, answer, ts, uptodate) {
		var item = {};
		item.Q = question;
		item.A = answer;
		if (ts === undefined) {
			ts = (new Date()).getTime() - 1392971679400;
		}
		item.T = ts;
		if (uptodate === undefined) {
			uptodate = 'N';
		}
		item.S = uptodate;
		localStorage.setItem('question-' + id, JSON.stringify(item));
	};

	AM.questionImport = function () {
		var lastimportts = localStorage.getItem('lastimportts'),
            importURL = 'http://www.isig.co.uk/api/quiz/questions/listupdatedsince/';
        if (!lastimportts) {
            lastimportts = 0;
        }
        importURL = importURL + lastimportts;
        $.ajax({
            url: importURL,
            type: 'GET',
            success: function (res) {
                res = JSON.parse(res);
                if (res.result !== undefined && res.result === 'success' && res.data !== undefined) {
                    AM.allQuestionsImportedOK(res.data);
                }
            }
        });
	};

    AM.allQuestionsImportedOK = function (data) {
		var i,
            j,
            question,
            insertedIDs = [];
		for (i = 0; i < localStorage.length; i = i + 1) {
			question = JSON.parse(localStorage.getItem(localStorage.key(i)));
            for (j = 0; j < data.length; j = j + 1) {
                if (question.T !== data[j].T && insertedIDs.indexOf(data[j].T) < 0) {
                    insertedIDs.push(data[j].T);
                    AM.addQuestion(j, data[j].Q, data[j].A, data[j].T, 'Y');
                }
            }
		}
        localStorage.setItem('lastimportts', (new Date()).getTime());
    };

    AM.questionExport = function () {
		var i,
            question,
			questions = [],
			exportJSON,
            exportURL,
            exportData;
		for (i = 0; i < localStorage.length; i = i + 1) {
			question = JSON.parse(localStorage.getItem(localStorage.key(i)));
			if (question.S === 'N') {
				questions.push(question);
			}
		}
		exportJSON = JSON.stringify(questions);
        exportURL = 'http://www.isig.co.uk/api/quiz/questions/add';
        exportData = {data: exportJSON };
        $.ajax({
            url: exportURL,
            type: 'POST',
            data: exportData,
            success: function (res) { 
                res = JSON.parse(res);
                if (res.result !== undefined && res.result === 'success') {
                    AM.allQuestionsExportedOK();
                }
            }
        });
	};


    AM.allQuestionsExportedOK = function () {
		var i,
            question,
            qid;
		for (i = 0; i < localStorage.length; i = i + 1) {
			question = JSON.parse(localStorage.getItem(localStorage.key(i)));
			if (question.S === 'N') {
                qid = localStorage.key(i).split('question-').join('');
                localStorage.removeItem(localStorage.key(i));
                AM.addQuestion(qid, question.Q, question.A, question.T, 'Y');
			}
		}
    };


	AM.getLevelFromID = function (id) {
		if (id < 10000000000000) {
			return 0;
		}
		if (id < 20000000000000) {
			return 1;
		}
		if (id < 30000000000000) {
			return 2;
		}
		if (id < 40000000000000) {
			return 3;
		}
		if (id < 50000000000000) {
			return 4;
		}
		if (id < 60000000000000) {
			return 5;
		}
		if (id < 70000000000000) {
			return 6;
		}
		if (id < 80000000000000) {
			return 7;
		}
		if (id < 90000000000000) {
			return 8;
		}
		if (id < 100000000000000) {
			return 9;
		}
		return 10;
	};

}());
