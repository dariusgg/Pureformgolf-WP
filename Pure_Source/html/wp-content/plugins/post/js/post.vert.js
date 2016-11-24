(function (doc, win, undef) {
	var dom, matchClasses = ['pw-float-left', 'pw-float-right'];

	dom = (function () {
		var dom = {};

		dom.addEvent = function (element, eventName, fn) {
			if (element.addEventListener) {
				element.addEventListener(eventName, fn, false);

			} else if (element.attachEvent) {
				element.attachEvent('on' + eventName, fn);

			} else {
				element['on' + eventName] = fn;

			}
		};

		dom.ready = function (fn) {
			var tryScroll,
				called = false,
				ready;

			ready = function () {
				if (called) { return; }

				called = true;

				fn();
			};

			tryScroll = function () {
				if (called){ return; }
				if (!doc.body){ return; }

				try {
					doc.documentElement.doScroll("left");

					ready();
				} catch(e) {
					setTimeout(tryScroll, 0);
				}
			};

			if (doc.addEventListener) {
				this.addEvent(doc, 'DOMContentLoaded', function () {
					ready();
				});

			} else if (doc.attachEvent) {
				if (doc.documentElement.doScroll && window === window.top) {
					tryScroll();
				}

				this.addEvent(doc, 'readystatechange', function () {
					if (doc.readyState === "complete") {
						ready();
					}
				});

			}

			this.addEvent(win, 'load', ready);
		};

		dom.getByClass = function (query) {
			var elements = [], regex, tmp = [], i, iLen;

			if (!doc.getElementsByClassName) {
				regex	= new RegExp("(^|\\s)" + query + "(\\s|$)");
				tmp		= doc.getElementsByTagName("*");
				iLen	= tmp.length;

				for (i = 0; i < iLen; i++) {
					if (regex.test(tmp[i].className)) {
						elements.push(tmp[i]);
					}
				}

			} else {
				elements = doc.getElementsByClassName(query);

			}

			return elements;
		};

		return dom;
	}());

	dom.ready(function () {
		var container = dom.getByClass(matchClasses[0])[0] || dom.getByClass(matchClasses[1])[0],
			alignVertical;
		
		var testCssEl = document.createElement("div");
			testCssEl.className = 'pw-test-uploadcss';
			testCssEl.style.fontSize = '1px';
			testCssEl.style.lineHeight = '1px';
			testCssEl.style.width = '1px';
			testCssEl.style.height = '1px';
		
		
		document.getElementsByTagName("body")[0].appendChild(testCssEl)

		alignVertical = function alignVertical () {
			var button = dom.getByClass('pw-icon')[0];
			if(button) {

				checkCss = function checkCss() {
					if(testCssEl.offsetHeight === 12 || testCssEl.offsetWidth === 14){
						var heightContainer = container.offsetHeight;
						container.style.marginTop = '-' + parseInt(heightContainer/2, 10) + 'px';
						testCssEl.removeNode;
					}
					else {
						setTimeout(checkCss, 200);
					}
					
				}
				checkCss()
				
			}
			else {
				setTimeout(alignVertical, 200);
			}

		};

		if (container) {
			alignVertical();
		}
	});

}(document, window, undefined));