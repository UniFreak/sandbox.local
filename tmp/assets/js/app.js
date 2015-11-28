"use strict";

// tips:初始化canvas开始

var game = document.getElementById('game'),
	ctx = game.getContext('2d'),
	cw = window.innerWidth,
	ch = window.innerHeight,
	gw = parseInt(game.getAttribute('width')),
	gh = parseInt(game.getAttribute('height'));
var gameObj = {
	add: function(arr) {
		arr.forEach(function(item, i) {
			objAdd(item, i, gameObj);
		});
	}
};
var personData = {
	boy: {
		images: ['assets/images/person/boy_1.png'],
		frames: {
			width: 250,
			height: 438,
			// count:图片总数
			count: 4
		},
		framerate: 4,
		animations: {
			stand: 0,
			run: [0, 3, 'run']
		}
	},
	girl: {
		images: ['assets/images/person/girl_1.png'],
		frames: {
			width: 250,
			height: 438,
			// count:图片总数
			count: 4
		},
		framerate: 4,
		animations: {
			stand: 0,
			run: [0, 3, 'run']
		}
	}
};
/**
 * 添加总数
 * 游戏时长
 */
var count = 0,
	gameLive = 100000,
	buttonClick = false;
// 初始化canvas结束


/*
	tips:load资源开始
 */
var queue = new createjs.LoadQueue(true);
queue.loadManifest(
	/*{
		src: "assets/js/manifest.json",
		type: "manifest"
	}*/
	[{
			"id": "boy",
			"src": "assets/images/person/boy_1.png"
		}, {
			"id": "girl",
			"src": "assets/images/person/girl_1.png"
		}, {
			"id": "btn_game_left",
			"src": "assets/images/btn/btn_game_left.png"
		}, {
			"id": "btn_game_right",
			"src": "assets/images/btn/btn_game_right.png"
		}, {
			"id": "bg",
			"src": "assets/images/bg/game_background.png"
		}, {
			"id": "table_bottom",
			"src": "assets/images/bg/game_table.png"
		}, {
			"id": "table_left",
			"src": "assets/images/bg/table_left.png"
		}, {
			"id": "table_right",
			"src": "assets/images/bg/table_right.png"
		}, {
			"id": "text_1",
			"src": "assets/images/text/text_1.png"
		}, {
			"id": "text_2",
			"src": "assets/images/text/text_2.png"
		}, {
			"id": "text_3",
			"src": "assets/images/text/text_3.png"
		}
		/*, {
						"id": "",
						"src": ""
					}, {
						"id": "",
						"src": ""
					}, {
						"id": "",
						"src": ""
					}, {
						"id": "",
						"src": ""
					}, {
						"id": "",
						"src": ""
					}, {
						"id": "",
						"src": ""
					}
					*/
	]
);
queue.on('fileload', fileloadIngHandle, this);
queue.on('complete', loadCompleteHandle, this);

function loadCompleteHandle(e) {
	console.info('complete');
	window._xbGame = new xbGame();
	_xbGame.init();
	_xbGame.start(gameObj);
};

function fileloadIngHandle(e) {
	console.log('loadIng');
};
/*
	load资源结束
 */


/**
 * tips:舞台build start
 */
var stage = new createjs.Stage(game);

// tips:设置帧率
createjs.Ticker.setFPS(20);
createjs.Ticker.addEventListener("tick", stage);

function tickHandle() {
	stage.update();
};
/**
 * 舞台build end
 */

/**
 * tips:game class start
 */
var xbGame = function() {
	this.screen = 'init';
};

xbGame.prototype.init = function() {
	var _gameContainer = new createjs.Container(),
		_personBox = new createjs.Container(),
		_mask = new createjs.Shape();

	var _bg = new createjs.Bitmap(queue.getResult('bg')),
		_table_bottom = new createjs.Bitmap(queue.getResult('table_bottom')),
		_table_left = new createjs.Bitmap(queue.getResult('table_left')),
		_table_right = new createjs.Bitmap(queue.getResult('table_right'));

	var _btn_game_left = new createjs.Bitmap(queue.getResult('btn_game_left')),
		_btn_game_right = new createjs.Bitmap(queue.getResult('btn_game_right'));

	var _text_1 = new createjs.Bitmap(queue.getResult('text_1')),
		_text_2 = new createjs.Bitmap(queue.getResult('text_2')),
		_text_3 = new createjs.Bitmap(queue.getResult('text_3'));

	// tips:储存各种createjs对象
	gameObj.add([{
		gameContainer: _gameContainer
	}, {
		personBox: _personBox
	}, {
		mask: _mask
	}, {
		btn_game_left: _btn_game_left
	}, {
		btn_game_right: _btn_game_right
	}, {
		table_left: _table_left
	}, {
		table_right: _table_right
	}, {
		table_bottom: _table_bottom
	}, {
		text_1: _text_1
	}, {
		text_2: _text_2
	}, {
		text_3: _text_3
	}]);

	// tips:设置位置 start
	_table_left.x = -_table_left.image.width;
	_table_right.x = gw;
	_table_right.y = _table_left.y = gh * 0.2;
	_table_bottom.y = 855;

	_btn_game_left.x = 0;
	_btn_game_right.x = gw * 0.7;

	_text_1.x = (gw - _text_1.image.width) / 2;
	_text_2.x = _text_3.x = (gw - _text_1.image.width) / 3;
	_text_1.y = _text_2.y = _text_3.y = (gh - _text_1.image.height) / 2 - 100;
	_text_1.alpha = _text_2.alpha = _text_3.alpha = 0;

	_btn_game_left.y = _btn_game_right.y = gw * 1.8;
	// 设置位置 end

	// tips:设置动画开始
	/*_table_left.alpha = _table_right.alpha = */
	_table_bottom.alpha = 0;

	_mask.graphics.beginFill('#545252').drawRect(0, 0, gw, gh).endFill();
	_mask.alpha = 0.5;
	// 设置动画结束

	// tips:自定义属性开始
	_btn_game_left.position = 'left';
	_btn_game_right.position = 'right';
	// 自定义属性结束

	// tips:事件监听开始
	_btn_game_left.addEventListener('click', function() {
		btnHandle(_btn_game_left);
	});
	_btn_game_right.addEventListener('click', function() {
		btnHandle(_btn_game_right);
	});
	// 事件监听结束

	// tips:添加到舞台开始
	_gameContainer.addChild(_bg);
	stage.addChild(_gameContainer);

	// tips:添加到舞台结束

	// tips:游戏开始
	// xbGame.prototype.start(gameObj);
};

xbGame.prototype.start = function(obj) {
	obj['gameContainer'].addChild(obj['table_bottom'], obj['table_left'], obj['table_right']);
	stage.addChild(obj['mask']);
	createjs.Tween.get(obj['table_bottom']).to({
		alpha: 1
	}, 900);
	createjs.Tween.get(obj['table_left']).to({
		x: 0
	}, 800);
	createjs.Tween.get(obj['table_right']).to({
		x: gw - (obj['table_right'].image.width)
	}, 800).call(function() {
		console.info('countDownStart');
		countDown(obj, 3);
	});

	// _gameContainer.addChild(obj[btn_game_left], obj[btn_game_right]);
};

xbGame.prototype.addPerson = function() {
	var _random = Math.floor(Math.random() * (10 - 1) + 1);
	var _sex = '';
	var _spriteSheet = null,
		_person_context = null;

	if (_random >= 5) {
		_sex = 'boy';
	} else {
		_sex = 'girl';
	};

	_spriteSheet = new createjs.SpriteSheet(personData[_sex]);
	_person_context = new createjs.Sprite(_spriteSheet, 'run');

	// _person_context.name = 'person_' + count;
	_person_context.y = -300;
	if (count % 2 == 0) {
		_person_context.x = gw * 0.3 - 30;
	} else {
		_person_context.x = gw * 0.3 + 30;
	};
	if (_sex == 'boy') {
		_person_context.xbPosition = 'left';
	} else {
		_person_context.xbPosition = 'right';
	};

	// sprite class
	// count 不断 ++
	// tips:bug
	// container class
	gameObj['personBox'].setChildIndex(_person_context, 100);
	console.log(gameObj['personBox'].getChildIndex(_person_context));

	gameObj['personBox'].addChildAt(_person_context);
	count++;
	createjs.Tween.get(_person_context).to({
		y: (5 - gameObj['personBox'].children.length) * 90
	}, 400).call(function() {
		if (gameObj['personBox'].children.length < 6) {
			xbGame.prototype.addPerson();
		} else {
			buttonClick = true;
			return false;
		};
	});
};

xbGame.prototype.end = function() {

};
xbGame.prototype.reset = function() {

};
/**
 * game class end
 */

/**
 * tips:button Event start
 */

function btnHandle(e) {
	// console.info(e.position);
	if (buttonClick) {
		if (gameObj['personBox'].children[0].xbPosition == e.position) {
			console.info('true');
		} else {
			console.info('false');
		};
		if (gameObj['personBox'].children[0].xbPosition == 'left') {
			createjs.Tween.get(gameObj['personBox'].children[0]).to({
				x: -400
			}, 300).call(removeChild);
		} else {
			createjs.Tween.get(gameObj['personBox'].children[0]).to({
				x: 900
			}, 300).call(removeChild);
		};
		buttonClick = false;
	} else {
		console.log('clickFalse');
	};
};

/**
 * button Event end
 */

// tips:引用方法开始
function objAdd(item, i, obj) {
	for (var _i in item) {
		obj[_i] = item[_i];
	};
};

function countDown(obj, val) {
	if (val == 0) {
		console.info('countDown_Ok');
		stage.removeChild(gameObj['mask']);
		stage.removeChild(gameObj['text_3']);
		stage.removeChild(gameObj['text_2']);
		stage.removeChild(gameObj['text_1']);
		stage.addChild(gameObj['personBox']);
		xbGame.prototype.addPerson(0);
		showBtn();
		// tips:重新设置帧率
		// createjs.Ticker.setFPS(6);
		return true;
	};
	if (val == 3) {
		stage.addChild(obj['text_1'], obj['text_2'], obj['text_3']);
	};
	createjs.Tween.get(obj['text_' + val]).to({
		alpha: 1,
		y: gh * 0.3
	}, 800).to({
		alpha: 0,
		y: gh * 0.4
	}, 200).call(function() {
		countDown(obj, (val - 1));
	});
};

function showBtn() {
	stage.addChild(gameObj['btn_game_left'], gameObj['btn_game_right']);
	createjs.Tween.get(gameObj['btn_game_left']).to({
		y: gh * 0.6
	}, 300);
	createjs.Tween.get(gameObj['btn_game_right']).to({
		y: gh * 0.6
	}, 300);
};

function removeChild() {
	gameObj['personBox'].removeChild(gameObj['personBox'].children[0]);
	for (var i = 0; i < gameObj['personBox'].children.length - 1; i++) {
		createjs.Tween.get(gameObj['personBox'].children[i]).to({
			y: gameObj['personBox'].children[i].y + 90
		}, 300);
	};
	xbGame.prototype.addPerson();
};
// 引用方法结束