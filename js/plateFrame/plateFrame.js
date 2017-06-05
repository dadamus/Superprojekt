/**
 * Created by Dawid on 01.06.2017.
 */

var dots = [];
var last_dot = 0;

var dots_position = [];

var dragId = -1;
var drawMode = "createMode";
var newDot = false;
var menuDot;

var areaValue;

var $frameCutterMenuCancel = $("#frameCutterMenuCancel");
function showCancelButton($positionBlock) {
	var position = $positionBlock.position();

	$frameCutterMenuCancel.css({
		left: (position.left - 40) + "px",
		top: position.top + "px"
	});
	$frameCutterMenuCancel.fadeIn();
}

function hideCancelButton() {
	$frameCutterMenuCancel.fadeOut();
}

function showDanger(message) {
	$.bootstrapGrowl(message, {
		ele: 'body', // which element to append to
		type: 'danger', // (null, 'info', 'danger', 'success')
		offset: {from: 'top', amount: 20}, // 'top', or 'bottom'
		align: 'center', // ('left', 'right', or 'center')
		width: 250, // (integer, or 'auto')
		delay: 4000, // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
		allow_dismiss: true, // If true then will display a cross to close the popup.
		stackup_spacing: 10 // spacing between consecutively stacked growls.
	});
}

function showSuccess(message) {
	$.bootstrapGrowl(message, {
		ele: 'body', // which element to append to
		type: 'success', // (null, 'info', 'danger', 'success')
		offset: {from: 'top', amount: 20}, // 'top', or 'bottom'
		align: 'center', // ('left', 'right', or 'center')
		width: 250, // (integer, or 'auto')
		delay: 4000, // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
		allow_dismiss: true, // If true then will display a cross to close the popup.
		stackup_spacing: 10 // spacing between consecutively stacked growls.
	});
}

var temp_pos;

function createDot(pos_x, pos_y, insertId) {
	var id;
	if (typeof insertId === 'undefined') {
		id = last_dot;
	} else {
		id = parseInt(insertId);
	}

	last_dot++;

	dots_position[id] = {
		pos_x: parseInt(pos_x),
		pos_y: parseInt(pos_y)
	};

	return '<div class="dot" id="' + id + '_dot" style="cursor: move; position: absolute; z-index: 100; background-color: #753b3b; width: 10px; height: 10px; border-radius: 20px !important; left: ' + pos_x + 'px; top: ' + pos_y + 'px"></div>';
}
var dotDiv_position;

function init()
{
	for(var p = 0; p < init_dots_position.length; p++) {
		createObjectDot(init_dots_position[p].pos_x, init_dots_position[p].pos_y);
	}
	drawConnections();
	$(".dot").draggable('disable');

	areaValue = Math.round(calculate());
	$("#wynik").html("Pole: " + areaValue + "mm^2");
}

function dragStartEvent(e, ui) {
	var $this = $(e.target);
	var dot_id = parseInt($this.attr("id"));
	dragId = dot_id;
}

function dragEvent(e, ui) {
	var $this = $(e.target);
	var dot_id = parseInt($this.attr("id"));

	dots_position[dot_id].pos_x = parseInt(e.pageX - dotDiv_position.left - 5);
	dots_position[dot_id].pos_y = parseInt(e.pageY - dotDiv_position.top - 5);
	drawConnections();
}

function dragEndEvent(e, ui) {
	dragId = -1;
	var $this = $(e.target);
	var dot_id = parseInt($this.attr("id"))

	dots_position[dot_id].pos_x = parseInt(e.pageX - dotDiv_position.left - 5);
	dots_position[dot_id].pos_y = parseInt(e.pageY - dotDiv_position.top - 5);
	newDot = false;
	drawConnections();

	if (drawMode !== "dragMode") {
		$(".dot").draggable('disable');
	}

	areaValue = Math.round(calculate());
	$("#wynik").html("Pole: " + areaValue + "mm^2");
}

function calculate()
{
	var area = 0;
	var area2 = 0
	for (var p = 0; p < dots_position.length; p++)
	{
		var next = 0;
		var prev = dots_position.length - 1;
		if (p + 1 < dots_position.length) {
			next = p+1;
		}
		if (p - 1 >= 0) {
			prev = p-1;
		}

		area += dots_position[p].pos_x * (dots_position[next].pos_y - dots_position[prev].pos_y);
		area2 += dots_position[p].pos_y * (dots_position[next].pos_x - dots_position[prev].pos_x);

	}

	area = Math.abs(area) / 2;
	return (area * imageSize.dpi * imageSize.dpi);
}

function drawConnections() {
	var $canvas = $("#dotConnections");
	$canvas.clearCanvas();

	var object = {
		strokeStyle: '#ff7ff6',
		strokeWidth: 2,
		rounded: true,
		closed: true,
	}

	for (var p = 0; p < dots_position.length; p++) {

		if (drawMode === "insertMode" && dragId === p && newDot === true) {
			continue;
		}

		if (typeof dots_position[p] === 'undefined') {
			continue;
		}

		object['x' + (p + 1)] = dots_position[p].pos_x + 5;
		object['y' + (p + 1)] = dots_position[p].pos_y + 5;
	}

	$canvas.drawLine(object);
}
var glow_selected = -1;

function insertNewDot(p1, p2) {
	var pos_x, pos_y;

	pos_x = (dots_position[p1].pos_x + dots_position[p2].pos_x) / 2;
	pos_y = (dots_position[p1].pos_y + dots_position[p2].pos_y) / 2;

	var insertId;
	if (p1 + 1 === p2) {
		insertId = p2;
	} else if (p2 + 1 === p1) {
		insertId = p1;
	} else {
		createObjectDot(pos_x, pos_y);
		return true;
	}

	var new_dots_position = [];
	for (var p = dots_position.length - 1; p >= 0; p--) {
		var newIndex = p;
		if (p >= insertId) {
			newIndex = p + 1;

			var $dot = $("#" + p + "_dot");
			$dot.attr("id", newIndex + "_dot");
		}

		new_dots_position[newIndex] = dots_position[p];
	}

	dots_position = new_dots_position.slice();
	createObjectDot(pos_x, pos_y, insertId);
	drawConnections();
}

$(document).ready(function () {
	dotDiv_position = $("#dots").offset();
	init();

	$("#deleteDot").on("click", function () {
		var $deleteDot = $("#" + menuDot + "_dot");
		$deleteDot.remove();

		if (menuDot === dots_position.length - 1) {
			 dots_position.splice(menuDot, 1);
		}

		var new_dots_position = [];
		for (var p = 0; p < dots_position.length; p++) {
			var newIndex = p;
			if (p > menuDot) {
				newIndex = p - 1;

				var $dot = $("#" + p + "_dot");
				$dot.attr("id", newIndex + "_dot");
			}

			new_dots_position[newIndex] = dots_position[p];
		}

		dots_position = new_dots_position.slice();
		last_dot--;
		drawConnections();
	});

	var $frameCutterMenu = $("#frameCutterMenu");
	$frameCutterMenu.on("click", "li", function () {
		var $this = $(this);
		var mode = $this.attr("id");

		if (mode === "insertMode" && dots_position.length < 2) {
			showDanger("Muszą istnieć przynajmniej 2 punkty!");
			return true;
		} else if (mode === "insertMode") {
			glow_selected = -1;
			$(".dot").addClass('glow');
		} else {
			hideCancelButton();
			$(".dot").removeClass("glow");
		}

		if (mode === "dragMode") {
			$(".dot").draggable('enable');
		} else {
			$(".dot").draggable('disable');
		}

		if (mode === "save") {
			if (areaValue === 0) {
				showDanger("Pole rowne 0!");
				return true;
			}

			$.ajax({
				data: "dots=" + JSON.stringify(dots_position) + "&areaValue=" + areaValue + "&f=" + frameId,
				url: site_path + "/engine/costing/plateFrame.php?a=addFrame",
				method: 'POST'
			}).done(function (msg) {
				if (msg === "ok") {
					showSuccess("Zapisalem!");
				}
			});

			return true;
		}

		$frameCutterMenu.find(".active").removeClass("active");
		$this.addClass("active");

		drawMode = mode;
	});

	$frameCutterMenuCancel.on("click", function () {
		if (drawMode === "insertMode") {
			$(".dot").addClass("glow");
			glow_selected = -1;
			hideCancelButton();
		}
	});

	$("#dots").on("mousedown", ".dot", function (e) {
		if (e.which === 3) {
			return true;
		}

		var $this = $(this);
		if ($this.hasClass("glow")) {
			var glowId = parseInt($this.attr("id"));
			if (glow_selected == -1) {
				glow_selected = glowId;
				$(".dot").removeClass("glow");

				var next, previous;
				previous = glow_selected - 1;
				next = glow_selected + 1;

				console.log("\n:" + glow_selected);
				console.log(previous);
				console.log(next);

				if (previous < 0) {
					previous = dots_position.length - 1;
				}
				if (next == dots_position.length) {
					next = 0;
				}

				$("#" + previous + "_dot").addClass("glow");
				$("#" + next + "_dot").addClass("glow");
				showCancelButton($("#insertMode"));
			} else {
				$(".dot").addClass("glow");
				insertNewDot(glow_selected, glowId);
				glow_selected = -1;
				$(".dot").addClass('glow');
				hideCancelButton();
			}
		}

		e.stopPropagation();
	});

	$("#dots").on("mousedown", function (e) {
		dotDiv_position = $("#dots").offset();

		if (e.which === 3 || drawMode === "insertMode" || drawMode === "dragMode") {
			return true;
		}

		var mouse_x = e.pageX - dotDiv_position.left - 5;
		var mouse_y = e.pageY - dotDiv_position.top - 5;

		newDot = true;
		var dot = createObjectDot(mouse_x, mouse_y);

		drawConnections();
		e.type = "mousedown.draggable";
		e.target = dot[0];
		dot.trigger(e);
		return false;
	});
});

function createObjectDot(pos_x, pos_y, insertId) {
	return $(createDot(pos_x, pos_y, insertId)).draggable({
		start: function (event, ui) {
			dragStartEvent(event, ui);
		},
		drag: function (event, ui) {
			dragEvent(event, ui);
		},
		stop: function (event, ui) {
			dragEndEvent(event, ui);
		}
	}).contextmenu({
		target: "#context-menu",
		before: function (e, context) {
			var $this = $(e.target);
			menuDot = parseInt($this.attr("id"));
			return true;
		}
	}).appendTo("#dots");
}