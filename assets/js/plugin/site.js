/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/*!
	 * dependsOn v1.5.0
	 * a jQuery plugin to facilitate the handling of form field dependencies.
	 *
	 * Copyright 2016 David Street
	 * Licensed under the MIT license.
	 */
	
	var SubjectController = __webpack_require__(1)
	
	/**
	 * Plugin access point.
	 * @param {Object} initialSet An object of key-value pairs of selectors and qualifiers
	 * representing the inital DependencySet.
	 * @param {Object} opts An object for key-value pairs of options.
	 * @return {SubjectController}
	 */
	$.fn.dependsOn = function(initialSet, opts) {
		var options = $.extend({}, {
			disable: true,
			hide: true,
			duration: 200,
			trigger: 'change'
		}, opts)
	
		// Namespace the trigger event
		options.trigger += (options.trigger.search('.dependsOn') > -1) ? '' :  '.dependsOn'
	
		var controller = new SubjectController(this, initialSet, options)
	
		return controller
	}


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * SubjectController
	 * ---
	 * Class which controls the state of the subject by responding its
	 * dependency collection state.
	 */
	
	var DependencyCollection = __webpack_require__(2)
	var DependencySet        = __webpack_require__(4)
	
	var SubjectController = function($subject, initialSet, options) {
		this.$subject = $subject
		this.collection = new DependencyCollection()
		this.options = $.extend({}, {
			onEnable: function() {},
			onDisable: function() {},
			trigger: 'change',
			readonly: false
		}, options)
		this.collection.addSet(new DependencySet(initialSet, this.options.trigger))
	
		this.$valueTarget = this._getValueTarget()
		this.isInitialState = true
	
		if (this.collection.qualified) {
			this._enable()
		} else {
			this._disable()
		}
	
		this.isInitialState = false
		this.collection.on('change', this._changeHandler.bind(this))
	}
	
	/**
	 * Change handler for the collection
	 * @param  {Object} state
	 * @private
	 */
	SubjectController.prototype._changeHandler = function(state) {
		if (state.qualified) {
			this._enable(state.triggerBy.$ele, state.e)
		} else {
			this._disable(state.triggerBy.$ele, state.e)
		}
	}
	
	/**
	 * Get the target element when setting a value
	 * @return {jQuery}
	 * @private
	 */
	SubjectController.prototype._getValueTarget = function() {
		var $valueTarget = this.$subject
	
		if (this.options.hasOwnProperty('valueTarget') && typeof this.options.valueTarget !== undefined) {
			$valueTarget = $(this.options.valueTarget)
	
		// If the subject is not a form field, then look for one within the subject
		} else if ( this.$subject[0].nodeName.toLowerCase() !== 'input' &&
			this.$subject[0].nodeName.toLowerCase() !== 'textarea' &&
			this.$subject[0].nodeName.toLowerCase() !== 'select') {
	
			$valueTarget = this.$subject.find('input, textarea, select')
		}
	
		return $valueTarget
	}
	
	/**
	 * Add a set to the dependency collection
	 * @param  {[type]} set DependencySet
	 * @return {SubjectController}
	 */
	SubjectController.prototype.or = function(set) {
		this.collection.addSet(new DependencySet(set, this.options.trigger))
	
		if (this.collection.qualified) {
			this._enable()
		} else {
			this._disable()
		}
	
		return this
	}
	
	/**
	 * Run a check of the collection
	 */
	SubjectController.prototype.check = function() {
		this.collection.runCheck()
	}
	
	/**
	 * Enable the subject
	 * @param  {Dependency} dependency The triggering dependency
	 * @param  {Event}      e The triggering DOM event
	 * @private
	 */
	SubjectController.prototype._enable = function(dependency, e) {
		if (this.options.disable) {
			this.$subject.attr('disabled', false)
		}
	
		if (this.options.readonly) {
			this.$subject.attr('readonly', false)
		}
	
		if (this.options.hide) {
			this._toggleDisplay(true, this.isInitialState)
		}
	
		if (this.options.hasOwnProperty('valueOnEnable') && typeof this.options.valueOnEnable !== undefined) {
			this.$valueTarget.val(this.options.valueOnEnable).change()
		}
	
		if (this.options.hasOwnProperty('checkOnEnable')) {
			this.$valueTarget.prop('checked', this.options.checkOnEnable).change()
		}
	
		if (this.options.hasOwnProperty('toggleClass') && typeof this.options.toggleClass !== undefined) {
			this.$subject.addClass(this.options.toggleClass)
		}
	
		this.options.onEnable.call(dependency, e, this.$subject)
	}
	
	/**
	 * Disable the subject
	 * @param  {Dependency} dependency The triggering dependency
	 * @param  {Event}      e The triggering DOM event
	 * @private
	 */
	SubjectController.prototype._disable = function(dependency, e) {
		if (this.options.disable) {
			this.$subject.attr('disabled', true)
		}
	
		if (this.options.readonly) {
			this.$subject.attr('readonly', true)
		}
	
		if (this.options.hide) {
			this._toggleDisplay(false, this.isInitialState)
		}
	
		if (this.options.hasOwnProperty('valueOnDisable') && typeof this.options.valueOnDisable !== undefined) {
			this.$valueTarget.val(this.options.valueOnDisable).change()
		}
	
		if (this.options.hasOwnProperty('checkOnDisable')) {
			this.$valueTarget.prop('checked', this.options.checkOnDisable).change()
		}
	
		if (this.options.hasOwnProperty('toggleClass') && typeof this.options.toggleClass !== undefined) {
			this.$subject.removeClass(this.options.toggleClass)
		}
	
		this.options.onDisable.call(dependency, e, this.$subject)
	}
	
	/**
	 * Show or hide the subject
	 * @param  {Boolean} show   Whether or not to show the element
	 * @param  {[type]}  noFade Whether or not to fade the element
	 * @private
	 */
	SubjectController.prototype._toggleDisplay = function(show, noFade) {
		var id = this.$subject.attr('id')
		var $hideEle
	
		if (this.$subject.parent()[0].nodeName.toLowerCase() === 'label') {
			$hideEle = this.$subject.parent()
		} else {
			$hideEle = this.$subject.add('label[for="' + id + '"]')
		}
	
		if (show && !$hideEle.is(':visible')) {
			if (noFade) {
				$hideEle.show()
			} else {
				$hideEle.fadeIn(this.options.duration)
			}
		} else if (!show) {
			if (noFade) {
				$hideEle.hide()
			} else {
				$hideEle.fadeOut(this.options.duration)
			}
		}
	}
	
	module.exports = SubjectController


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * DependencyCollection
	 * ---
	 * Class which defines a collection of dependency sets.
	 * Each set defines a logical OR, so the collection is considered qualified
	 * when _any_ of the sets are qualified.
	 */
	
	var EventEmitter  = __webpack_require__(3).EventEmitter
	
	var DependencyCollection = function() {
		this.sets = []
	
		// Keep track of how many sets are qualified.
		// Qualified sets will add 1, unqualified sets will subtract 1 unless the
		// sum is 0. The sum must not fall below zero.
		this._qualSum = 0
		this.qualified = null
	}
	
	module.exports = DependencyCollection
	
	DependencyCollection.prototype = $.extend({}, EventEmitter.prototype)
	
	/**
	 * Add a dependency set to the collection
	 * @param  {DependencySet} set
	 */
	DependencyCollection.prototype.addSet = function(set) {
		this.sets.push(set)
		this._qualSum += set.qualified ? 1 : 0
		this.qualified = this._qualSum > 0
	
		set.on('change', this._setChangeHandler.bind(this))
	}
	
	/**
	 * Check to see if the collection can qualify by checking each set
	 */
	DependencyCollection.prototype.runCheck = function() {
		for (var i = 0, len = this.sets.length; i < len; i++) {
			this.sets[i].runCheck()
		}
	}
	
	/**
	 * Handler for a set's `change` event
	 * Emit a `change` event when the qualfied status of the collection changes
	 * @param  {Object} state
	 * @private
	 */
	DependencyCollection.prototype._setChangeHandler = function(state) {
		var prevState = this.qualified
		this._qualSum += state.qualified ? 1 : this._qualSum === 0 ? 0 : -1
		this.qualified = this._qualSum > 0
		
		if (this.qualified !== prevState) {
			this.emit('change', {
				triggerBy: state.triggerBy,
				e: state.e,
				qualified: this.qualified
			})
		}
	}


/***/ },
/* 3 */
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	function EventEmitter() {
	  this._events = this._events || {};
	  this._maxListeners = this._maxListeners || undefined;
	}
	module.exports = EventEmitter;
	
	// Backwards-compat with node 0.10.x
	EventEmitter.EventEmitter = EventEmitter;
	
	EventEmitter.prototype._events = undefined;
	EventEmitter.prototype._maxListeners = undefined;
	
	// By default EventEmitters will print a warning if more than 10 listeners are
	// added to it. This is a useful default which helps finding memory leaks.
	EventEmitter.defaultMaxListeners = 10;
	
	// Obviously not all Emitters should be limited to 10. This function allows
	// that to be increased. Set to zero for unlimited.
	EventEmitter.prototype.setMaxListeners = function(n) {
	  if (!isNumber(n) || n < 0 || isNaN(n))
	    throw TypeError('n must be a positive number');
	  this._maxListeners = n;
	  return this;
	};
	
	EventEmitter.prototype.emit = function(type) {
	  var er, handler, len, args, i, listeners;
	
	  if (!this._events)
	    this._events = {};
	
	  // If there is no 'error' event listener then throw.
	  if (type === 'error') {
	    if (!this._events.error ||
	        (isObject(this._events.error) && !this._events.error.length)) {
	      er = arguments[1];
	      if (er instanceof Error) {
	        throw er; // Unhandled 'error' event
	      }
	      throw TypeError('Uncaught, unspecified "error" event.');
	    }
	  }
	
	  handler = this._events[type];
	
	  if (isUndefined(handler))
	    return false;
	
	  if (isFunction(handler)) {
	    switch (arguments.length) {
	      // fast cases
	      case 1:
	        handler.call(this);
	        break;
	      case 2:
	        handler.call(this, arguments[1]);
	        break;
	      case 3:
	        handler.call(this, arguments[1], arguments[2]);
	        break;
	      // slower
	      default:
	        args = Array.prototype.slice.call(arguments, 1);
	        handler.apply(this, args);
	    }
	  } else if (isObject(handler)) {
	    args = Array.prototype.slice.call(arguments, 1);
	    listeners = handler.slice();
	    len = listeners.length;
	    for (i = 0; i < len; i++)
	      listeners[i].apply(this, args);
	  }
	
	  return true;
	};
	
	EventEmitter.prototype.addListener = function(type, listener) {
	  var m;
	
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  if (!this._events)
	    this._events = {};
	
	  // To avoid recursion in the case that type === "newListener"! Before
	  // adding it to the listeners, first emit "newListener".
	  if (this._events.newListener)
	    this.emit('newListener', type,
	              isFunction(listener.listener) ?
	              listener.listener : listener);
	
	  if (!this._events[type])
	    // Optimize the case of one listener. Don't need the extra array object.
	    this._events[type] = listener;
	  else if (isObject(this._events[type]))
	    // If we've already got an array, just append.
	    this._events[type].push(listener);
	  else
	    // Adding the second element, need to change to array.
	    this._events[type] = [this._events[type], listener];
	
	  // Check for listener leak
	  if (isObject(this._events[type]) && !this._events[type].warned) {
	    if (!isUndefined(this._maxListeners)) {
	      m = this._maxListeners;
	    } else {
	      m = EventEmitter.defaultMaxListeners;
	    }
	
	    if (m && m > 0 && this._events[type].length > m) {
	      this._events[type].warned = true;
	      console.error('(node) warning: possible EventEmitter memory ' +
	                    'leak detected. %d listeners added. ' +
	                    'Use emitter.setMaxListeners() to increase limit.',
	                    this._events[type].length);
	      if (typeof console.trace === 'function') {
	        // not supported in IE 10
	        console.trace();
	      }
	    }
	  }
	
	  return this;
	};
	
	EventEmitter.prototype.on = EventEmitter.prototype.addListener;
	
	EventEmitter.prototype.once = function(type, listener) {
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  var fired = false;
	
	  function g() {
	    this.removeListener(type, g);
	
	    if (!fired) {
	      fired = true;
	      listener.apply(this, arguments);
	    }
	  }
	
	  g.listener = listener;
	  this.on(type, g);
	
	  return this;
	};
	
	// emits a 'removeListener' event iff the listener was removed
	EventEmitter.prototype.removeListener = function(type, listener) {
	  var list, position, length, i;
	
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  if (!this._events || !this._events[type])
	    return this;
	
	  list = this._events[type];
	  length = list.length;
	  position = -1;
	
	  if (list === listener ||
	      (isFunction(list.listener) && list.listener === listener)) {
	    delete this._events[type];
	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);
	
	  } else if (isObject(list)) {
	    for (i = length; i-- > 0;) {
	      if (list[i] === listener ||
	          (list[i].listener && list[i].listener === listener)) {
	        position = i;
	        break;
	      }
	    }
	
	    if (position < 0)
	      return this;
	
	    if (list.length === 1) {
	      list.length = 0;
	      delete this._events[type];
	    } else {
	      list.splice(position, 1);
	    }
	
	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);
	  }
	
	  return this;
	};
	
	EventEmitter.prototype.removeAllListeners = function(type) {
	  var key, listeners;
	
	  if (!this._events)
	    return this;
	
	  // not listening for removeListener, no need to emit
	  if (!this._events.removeListener) {
	    if (arguments.length === 0)
	      this._events = {};
	    else if (this._events[type])
	      delete this._events[type];
	    return this;
	  }
	
	  // emit removeListener for all listeners on all events
	  if (arguments.length === 0) {
	    for (key in this._events) {
	      if (key === 'removeListener') continue;
	      this.removeAllListeners(key);
	    }
	    this.removeAllListeners('removeListener');
	    this._events = {};
	    return this;
	  }
	
	  listeners = this._events[type];
	
	  if (isFunction(listeners)) {
	    this.removeListener(type, listeners);
	  } else if (listeners) {
	    // LIFO order
	    while (listeners.length)
	      this.removeListener(type, listeners[listeners.length - 1]);
	  }
	  delete this._events[type];
	
	  return this;
	};
	
	EventEmitter.prototype.listeners = function(type) {
	  var ret;
	  if (!this._events || !this._events[type])
	    ret = [];
	  else if (isFunction(this._events[type]))
	    ret = [this._events[type]];
	  else
	    ret = this._events[type].slice();
	  return ret;
	};
	
	EventEmitter.prototype.listenerCount = function(type) {
	  if (this._events) {
	    var evlistener = this._events[type];
	
	    if (isFunction(evlistener))
	      return 1;
	    else if (evlistener)
	      return evlistener.length;
	  }
	  return 0;
	};
	
	EventEmitter.listenerCount = function(emitter, type) {
	  return emitter.listenerCount(type);
	};
	
	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	
	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	
	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	
	function isUndefined(arg) {
	  return arg === void 0;
	}


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * DependencySet
	 * ---
	 * Class which defines a set of dependencies
	 */
	
	var EventEmitter = __webpack_require__(3).EventEmitter
	var Dependency   = __webpack_require__(5)
	
	var DependencySet = function(dependencies, trigger) {
		this.dependencies = []
	
		// Keep track of how many dependencies are qualified.
		// Qualified dependencies will add 1, unqualified dependencies will
		// subtract 1 unless the sum is 0. The sum must not fall below zero.
		var qualSum = 0
	
		for (var d in dependencies) {
			if (!dependencies.hasOwnProperty(d)) continue
	
			var newDep = new Dependency(d, dependencies[d], trigger)
			this.dependencies.push(newDep)
			qualSum += newDep.qualified ? 1 : 0
	
			newDep.on('change', getDepChangeHandler(newDep).bind(this))
		}
	
		this.doesQualify = function() {
			return qualSum === this.dependencies.length
		}
	
		// Set initial state of the set
		this.qualified = this.doesQualify()
	
		function getDepChangeHandler(dep) {
			return function(state) {
				var prevState = this.qualified
				qualSum += state.qualified ? 1 : qualSum === 0 ? 0 : -1
				this.qualified = this.doesQualify()
	
				if (this.qualified !== prevState) {
					this.emit('change', {
						triggerBy: dep,
						e: state.e,
						qualified: this.doesQualify()
					})
				}
			}
		}
	}
	
	module.exports = DependencySet
	
	DependencySet.prototype = $.extend({}, EventEmitter.prototype)
	
	/**
	 * Run a qualification check of all dependencies
	 */
	DependencySet.prototype.runCheck = function() {
		for (var i = 0, len = this.dependencies.length; i < len; i++) {
			this.dependencies[i].runCheck()
		}
	}


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Dependency
	 * ---
	 * Class which defines dependency qualifiers
	 */
	
	
	var EventEmitter = __webpack_require__(3).EventEmitter
	
	var Dependency = function(selector, qualifiers, trigger) {
		this.$ele = $(selector)
		this.qualifiers = qualifiers
		this.fieldState = getFieldState(this.$ele)
		this.methods = [
			'enabled',
			'checked',
			'values',
			'not',
			'match',
			'contains',
			'email',
			'url'
		]
	
		// Set initial state of the dependency
		this.qualified = this.doesQualify()
	
		this.$ele.on(trigger, handler.bind(this))
		this.runCheck = handler.bind(this)
	
		function handler(e) {
			var prevState = this.qualified
	
			this.fieldState = getFieldState(this.$ele)
			this.qualified = this.doesQualify()
	
			if (this.qualified !== prevState) {
				this.emit('change', {
					selector: selector,
					e: e,
					qualified: this.qualified
				})
			}
		}
	}
	
	Dependency.prototype = $.extend({}, EventEmitter.prototype)
	
	/**
	 * Qualifier method which checks for the `disabled` attribute.
	 * ---
	 * Returns false when dependency is disabled and `enabled`
	 * qualifier is true *or* when dependency is not disabled and
	 * `enabled` qualifier is false.
	 * Returns true otherwise.
	 *
	 * @param {Boolean} enabledVal The value we are checking
	 * @return {Boolean}
	 */
	Dependency.prototype.enabled = function(enabledVal) {
		if ((!this.fieldState.disabled && enabledVal) ||
			(this.fieldState.disabled && !enabledVal)) {
			return true
		}
	
		return false
	}
	
	/**
	 * Qualifier method which checks for the `checked` attribute on
	 * checkboxes and radio buttons.
	 * ---
	 * Dependency must be a checkbox for this qualifier.
	 * Returns false if checkbox is not checked and the `checked` qualifier
	 * is true *or* the checkbox is checked and the `checked` qualifier
	 * is false.
	 *
	 * @param {Boolean} checkedVal The value we are checking.
	 * @return {Boolean}
	 */
	Dependency.prototype.checked = function(checkedVal) {
		if (this.$ele.attr('type') === 'checkbox') {
			if ((!this.fieldState.checked && checkedVal) ||
				(this.fieldState.checked && !checkedVal)) {
				return false
			}
		}
	
		return true
	}
	
	/**
	 * Qualifier method which checks the dependency input value against an
	 * array of whitelisted values.
	 * ---
	 * For single value dependency, returns true if values match.
	 * When dependency value is an array, returns true if array compares to
	 * a single value in the whitlist.
	 * This is helpful when dealing with a multiselect input, and comparing
	 * against an array of value sets:
	 * 		[ [1, 2, 3], [4, 5, 6], [7, 8] ]
	 *
	 * @param  {Array} whitelist The list of acceptable values
	 * @return {Boolean}
	 */
	Dependency.prototype.values = function(whitelist) {
		for (var i = 0, len = whitelist.length; i < len; i++) {
			if (this.fieldState.value !== null && Array.isArray(this.fieldState.value)) {
				if ($(this.fieldState.value).not(whitelist[i]).length === 0 &&
					$(whitelist[i]).not(this.fieldState.value).length === 0) {
					return true
				}
			} else if (whitelist[i] === this.fieldState.value) {
				return true
			}
		}
	
		return false
	}
	
	/**
	 * Qualifier method which checks the dependency input value against an
	 * array of blacklisted values.
	 * ---
	 * Returns true when the dependency value is not in the blacklist.
	 *
	 * @param  {Array} blacklist The list of unacceptable values
	 * @return {Boolean}
	 */
	Dependency.prototype.not = function(blacklist) {
		return !this.values(blacklist)
	}
	
	/**
	 * Qualifier method which runs a RegEx pattern match on the dependency
	 * input value.
	 * ---
	 * Returns true when the dependency value matches the regular expression.
	 * If dependency value is an array, will only return true if _all_ values
	 * match the regular expression.
	 *
	 * @param  {RegExp} regex Regular expression to test against
	 * @return {Boolean}
	 */
	Dependency.prototype.match = function(regex) {
		var val = this.fieldState.value
	
		if (!Array.isArray(this.fieldState.value)) {
			val = [val]
		}
	
		for (var i = 0, len = val.length; i < len; i++) {
			if (!regex.test(val[i])) return false
		}
	
		return true
	}
	
	/**
	 * Qualifier method which runs a RegExp pattern match on the dependency
	 * input value.
	 * ---
	 * Returns true when the dependency value does *not* match the regexp.
	 * If dependency value is an array, will only return true if _none_ of the
	 * values match the regular expression.
	 *
	 * @param  {RegExp} regex Regular expression to test against
	 * @return {Boolean}
	 */
	Dependency.prototype.notMatch = function(regex) {
		var val = this.fieldState.value
	
		if (!Array.isArray(this.fieldState.value)) {
			val = [val]
		}
	
		for (var i = 0, len = val.length; i < len; i++) {
			if (regex.test(val[i])) return false
		}
	
		return true
	}
	
	/**
	 * Qualifier method which checks if a whitelisted value exists in an
	 * array of dependency values.
	 * ---
	 * Useful for select inputs with the `multiple` attribute.
	 * If dependency value is not an array, the method will fallback to the
	 * `values` qualifier.
	 *
	 * @param  {Array} whitelist List of acceptable values
	 * @return {Boolean}
	 */
	Dependency.prototype.contains = function(whitelist) {
		if (!Array.isArray(this.fieldState.value)) {
			return this.values(whitelist)
		}
	
		for (var i = 0, len = whitelist.length; i < len; i++) {
			if ($.inArray(whitelist[i], this.fieldState.value) !== -1) {
				return true
			}
		}
	
		return false
	}
	
	/**
	 * Qualifier method which checks that the value is a valid email address
	 * ---
	 * Returns true when the value is an email address and `shouldMatch` is
	 * true *or* when value is not an email address and `shouldMatch`
	 * is false.
	 *
	 * @param  {Boolean} shouldMatch Should the value be valid
	 * @return {Boolean}
	 */
	Dependency.prototype.email = function(shouldMatch) {
		var regex = /^[_a-zA-Z0-9\-\+]+(\.[_a-zA-Z0-9\-\+]+)*@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))$/
	
		return this.match(regex) === shouldMatch
	}
	
	/**
	 * Qualifier method which checks that the value is a valid URL
	 * ---
	 * Returns true when the value is a URL and `shouldMatch` is true *or*
	 * when value is not a URL and `shouldMatch` is false.
	 *
	 * @param  {Boolean} shouldMatch Should the value be valid
	 * @return {Boolean}
	 */
	Dependency.prototype.url = function(shouldMatch) {
		var regex = /(((http|ftp|https):\/\/)|www\.)[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?\^=%&:\/~\+#!]*[\w\-\@?\^=%&\/~\+#])?/
	
		return this.match(regex) === shouldMatch
	}
	
	/**
	 * Qualifier method which checks that the value within an inclusive
	 * numerical range
	 * ---
	 * Returns true when the value falls within the range. Alpha characters can
	 * also be evaluated, and will only be considered valid when the range values
	 * are also apha characters.
	 *
	 * @param  {Number|Character} start The range start
	 * @param  {Number|Character} end The range extend
	 * @param  {Number}           [step] The number of steps
	 * @return {Boolean}
	 */
	Dependency.prototype.range = function(start, end, step) {
		var type = typeof start === 'string' ? 'char' : 'number'
		var startVal = type === 'char' ? start.charCodeAt() : start
		var endVal = type === 'char' ? end.charCodeAt() : end
		var val = type === 'char' ? this.fieldState.value.charCodeAt() : parseFloat(this.fieldState.value)
	
		if (step) {
			var valArray = []
			for (var i = startVal; i <= endVal; i += step) valArray.push(i)
			return valArray.indexOf(val) >= 0
		} else {
			if (val >= startVal && val <= endVal) {
				return true
			}
		}
	
		return false
	}
	
	/**
	 * Check the dependency value against all of its qualifiers. If
	 * qualifiers contains an unknown qualifier, treat it as a custom
	 * qualifier and execute the function.
	 *
	 * @return {Boolean}
	 */
	Dependency.prototype.doesQualify = function() {
		for (var q in this.qualifiers) {
			if (!this.qualifiers.hasOwnProperty(q)) continue
	
			if (this.methods.indexOf(q) && typeof this[q] === 'function') {
				if (q === 'range') {
					if (!this[q].apply(this, this.qualifiers[q])) {
						return false
					}
				} else {
					if (!this[q].call(this, this.qualifiers[q])) {
						return false
					}
				}
			} else if (typeof this.qualifiers[q] === 'function') {
				if (!this.qualifiers[q].call(this.qualifiers, this.$ele.val())) {
					return false
				}
			}
		}
	
		return true
	}
	
	module.exports = Dependency
	
	/**
	 * Get the current state of a field
	 * @param  {jQuery} $ele The element
	 * @return {Object}
	 * @private
	 */
	function getFieldState($ele) {
		var val = $ele.val()
	
		// If dependency is a radio group, then filter by `:checked`
		if ($ele.attr('type') === 'radio') {
			val = $ele.filter(':checked').val()
		}
	
		return {
			value: val,
			checked: $ele.is(':checked'),
			disabled: $ele.is(':disabled'),
			selected: $ele.is(':selected')
		}
	}
	
	// Array.isArray polyfill
	if (!Array.isArray) {
		Array.isArray = function(arg) {
			return Object.prototype.toString.call(arg) === '[object Array]'
		}
	}
	
	// Number.isNaN polyfill
	Number.isNaN = Number.isNaN || function(value) {
		return value !== value
	}


/***/ }
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNzlmZmFjZWIxNzVhMWU5NDQ3ZjUiLCJ3ZWJwYWNrOi8vLy4vc3JjL2RlcGVuZHNPbi5qcyIsIndlYnBhY2s6Ly8vLi9zcmMvc3ViamVjdC1jb250cm9sbGVyLmpzIiwid2VicGFjazovLy8uL3NyYy9kZXBlbmRlbmN5LWNvbGxlY3Rpb24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzIiwid2VicGFjazovLy8uL3NyYy9kZXBlbmRlbmN5LXNldC5qcyIsIndlYnBhY2s6Ly8vLi9zcmMvZGVwZW5kZW5jeS5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsdUJBQWU7QUFDZjtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7OztBQ3RDQTtBQUNBLGlCQUFnQjtBQUNoQjtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQSxZQUFXLE9BQU87QUFDbEI7QUFDQSxZQUFXLE9BQU87QUFDbEIsYUFBWTtBQUNaO0FBQ0E7QUFDQSwyQkFBMEI7QUFDMUI7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFFOztBQUVGO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTs7Ozs7OztBQy9CQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSw0QkFBMkI7QUFDM0IsMEJBQXlCO0FBQ3pCLDJCQUEwQjtBQUMxQjtBQUNBO0FBQ0EsR0FBRTtBQUNGOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLEdBQUU7QUFDRjtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsYUFBWSxPQUFPO0FBQ25CO0FBQ0E7QUFD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Q0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQSxJQUFHO0FBQ0gscUJBQW9CLFNBQVM7QUFDN0I7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsTUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBLElBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7Ozs7Ozs7QUN6U0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxNQUFLO0FBQ0w7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUEsc0NB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QUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJzaXRlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pXG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG5cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGV4cG9ydHM6IHt9LFxuIFx0XHRcdGlkOiBtb2R1bGVJZCxcbiBcdFx0XHRsb2FkZWQ6IGZhbHNlXG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmxvYWRlZCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oMCk7XG5cblxuXG4vKiogV0VCUEFDSyBGT09URVIgKipcbiAqKiB3ZWJwYWNrL2Jvb3RzdHJhcCA3OWZmYWNlYjE3NWExZTk0NDdmNVxuICoqLyIsIi8qIVxuICogZGVwZW5kc09uIHYke3ZlcnNpb259XG4gKiBhIGpRdWVyeSBwbHVnaW4gdG8gZmFjaWxpdGF0ZSB0aGUgaGFuZGxpbmcgb2YgZm9ybSBmaWVsZCBkZXBlbmRlbmNpZXMuXG4gKlxuICogQ29weXJpZ2h0IDIwMTYgRGF2aWQgU3RyZWV0XG4gKiBMaWNlbnNlZCB1bmRlciB0aGUgTUlUIGxpY2Vuc2UuXG4gKi9cblxudmFyIFN1YmplY3RDb250cm9sbGVyID0gcmVxdWlyZSgnLi9zdWJqZWN0LWNvbnRyb2xsZXInKVxuXG4vKipcbiAqIFBsdWdpbiBhY2Nlc3MgcG9pbnQuXG4gKiBAcGFyYW0ge09iamVjdH0gaW5pdGlhbFNldCBBbiBvYmplY3Qgb2Yga2V5LXZhbHVlIHBhaXJzIG9mIHNlbGVjdG9ycyBhbmQgcXVhbGlmaWVyc1xuICogcmVwcmVzZW50aW5nIHRoZSBpbml0YWwgRGVwZW5kZW5jeVNldC5cbiAqIEBwYXJhbSB7T2JqZWN0fSBvcHRzIEFuIG9iamVjdCBmb3Iga2V5LXZhbHVlIHBhaXJzIG9mIG9wdGlvbnMuXG4gKiBAcmV0dXJuIHtTdWJqZWN0Q29udHJvbGxlcn1cbiAqL1xuJC5mbi5kZXBlbmRzT24gPSBmdW5jdGlvbihpbml0aWFsU2V0LCBvcHRzKSB7XG5cdHZhciBvcHRpb25zID0gJC5leHRlbmQoe30sIHtcblx0XHRkaXNhYmxlOiB0cnVlLFxuXHRcdGhpZGU6IHRydWUsXG5cdFx0ZHVyYXRpb246IDIwMCxcblx0XHR0cmlnZ2VyOiAnY2hhbmdlJ1xuXHR9LCBvcHRzKVxuXG5cdC8vIE5hbWVzcGFjZSB0aGUgdHJpZ2dlciBldmVudFxuXHRvcHRpb25zLnRyaWdnZXIgKz0gKG9wdGlvbnMudHJpZ2dlci5zZWFyY2goJy5kZXBlbmRzT24nKSA+IC0xKSA/ICcnIDogICcuZGVwZW5kc09uJ1xuXG5cdHZhciBjb250cm9sbGVyID0gbmV3IFN1YmplY3RDb250cm9sbGVyKHRoaXMsIGluaXRpYWxTZXQsIG9wdGlvbnMpXG5cblx0cmV0dXJuIGNvbnRyb2xsZXJcbn1cblxuXG5cbi8qKioqKioqKioqKioqKioqKlxuICoqIFdFQlBBQ0sgRk9PVEVSXG4gKiogLi9zcmMvZGVwZW5kc09uLmpzXG4gKiogbW9kdWxlIGlkID0gMFxuICoqIG1vZHVsZSBjaHVua3MgPSAwXG4gKiovIiwiLyoqXG4gKiBTdWJqZWN0Q29udHJvbGxlclxuICogLS0tXG4gKiBDbGFzcyB3aGljaCBjb250cm9scyB0aGUgc3RhdGUgb2YgdGhlIHN1YmplY3QgYnkgcmVzcG9uZGluZyBpdHNcbiAqIGRlcGVuZGVuY3kgY29sbGVjdGlvbiBzdGF0ZS5cbiAqL1xuXG52YXIgRGVwZW5kZW5jeUNvbGxlY3Rpb24gPSByZXF1aXJlKCcuL2RlcGVuZGVuY3ktY29sbGVjdGlvbicpXG52YXIgRGVwZW5kZW5jeVNldCAgICAgICAgPSByZXF1aXJlKCcuL2RlcGVuZGVuY3ktc2V0JylcblxudmFyIFN1YmplY3RDb250cm9sbGVyID0gZnVuY3Rpb24oJHN1YmplY3QsIGluaXRpYWxTZXQsIG9wdGlvbnMpIHtcblx0dGhpcy4kc3ViamVjdCA9ICRzdWJqZWN0XG5cdHRoaXMuY29sbGVjdGlvbiA9IG5ldyBEZXBlbmRlbmN5Q29sbGVjdGlvbigpXG5cdHRoaXMub3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCB7XG5cdFx0b25FbmFibGU6IGZ1bmN0aW9uKCkge30sXG5cdFx0b25EaXNhYmxlOiBmdW5jdGlvbigpIHt9LFxuXHRcdHRyaWdnZXI6ICdjaGFuZ2UnLFxuXHRcdHJlYWRvbmx5OiBmYWxzZVxuXHR9LCBvcHRpb25zKVxuXHR0aGlzLmNvbGxlY3Rpb24uYWRkU2V0KG5ldyBEZXBlbmRlbmN5U2V0KGluaXRpYWxTZXQsIHRoaXMub3B0aW9ucy50cmlnZ2VyKSlcblxuXHR0aGlzLiR2YWx1ZVRhcmdldCA9IHRoaXMuX2dldFZhbHVlVGFyZ2V0KClcblx0dGhpcy5pc0luaXRpYWxTdGF0ZSA9IHRydWVcblxuXHRpZiAodGhpcy5jb2xsZWN0aW9uLnF1YWxpZmllZCkge1xuXHRcdHRoaXMuX2VuYWJsZSgpXG5cdH0gZWxzZSB7XG5cdFx0dGhpcy5fZGlzYWJsZSgpXG5cdH1cblxuXHR0aGlzLmlzSW5pdGlhbFN0YXRlID0gZmFsc2Vcblx0dGhpcy5jb2xsZWN0aW9uLm9uKCdjaGFuZ2UnLCB0aGlzLl9jaGFuZ2VIYW5kbGVyLmJpbmQodGhpcykpXG59XG5cbi8qKlxuICogQ2hhbmdlIGhhbmRsZXIgZm9yIHRoZSBjb2xsZWN0aW9uXG4gKiBAcGFyYW0gIHtPYmplY3R9IHN0YXRlXG4gKiBAcHJpdmF0ZVxuICovXG5TdWJqZWN0Q29udHJvbGxlci5wcm90b3R5cGUuX2NoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbihzdGF0ZSkge1xuXHRpZiAoc3RhdGUucXVhbGlmaWVkKSB7XG5cdFx0dGhpcy5fZW5hYmxlKHN0YXRlLnRyaWdnZXJCeS4kZWxlLCBzdGF0ZS5lKVxuXHR9IGVsc2Uge1xuXHRcdHRoaXMuX2Rpc2FibGUoc3RhdGUudHJpZ2dlckJ5LiRlbGUsIHN0YXRlLmUpXG5cdH1cbn1cblxuLyoqXG4gKiBHZXQgdGhlIHRhcmdldCBlbGVtZW50IHdoZW4gc2V0dGluZyBhIHZhbHVlXG4gKiBAcmV0dXJuIHtqUXVlcnl9XG4gKiBAcHJpdmF0ZVxuICovXG5TdWJqZWN0Q29udHJvbGxlci5wcm90b3R5cGUuX2dldFZhbHVlVGFyZ2V0ID0gZnVuY3Rpb24oKSB7XG5cdHZhciAkdmFsdWVUYXJnZXQgPSB0aGlzLiRzdWJqZWN0XG5cblx0aWYgKHRoaXMub3B0aW9ucy5oYXNPd25Qcm9wZXJ0eSgndmFsdWVUYXJnZXQnKSAmJiB0eXBlb2YgdGhpcy5vcHRpb25zLnZhbHVlVGFyZ2V0ICE9PSB1bmRlZmluZWQpIHtcblx0XHQkdmFsdWVUYXJnZXQgPSAkKHRoaXMub3B0aW9ucy52YWx1ZVRhcmdldClcblxuXHQvLyBJZiB0aGUgc3ViamVjdCBpcyBub3QgYSBmb3JtIGZpZWxkLCB0aGVuIGxvb2sgZm9yIG9uZSB3aXRoaW4gdGhlIHN1YmplY3Rcblx0fSBlbHNlIGlmICggdGhpcy4kc3ViamVjdFswXS5ub2RlTmFtZS50b0xvd2VyQ2FzZSgpICE9PSAnaW5wdXQnICYmXG5cdFx0dGhpcy4kc3ViamVjdFswXS5ub2RlTmFtZS50b0xvd2VyQ2FzZSgpICE9PSAndGV4dGFyZWEnICYmXG5cdFx0dGhpcy4kc3ViamVjdFswXS5ub2RlTmFtZS50b0xvd2VyQ2FzZSgpICE9PSAnc2VsZWN0Jykge1xuXG5cdFx0JHZhbHVlVGFyZ2V0ID0gdGhpcy4kc3ViamVjdC5maW5kKCdpbnB1dCwgdGV4dGFyZWEsIHNlbGVjdCcpXG5cdH1cblxuXHRyZXR1cm4gJHZhbHVlVGFyZ2V0XG59XG5cbi8qKlxuICogQWRkIGEgc2V0IHRvIHRoZSBkZXBlbmRlbmN5IGNvbGxlY3Rpb25cbiAqIEBwYXJhbSAge1t0eXBlXX0gc2V0IERlcGVuZGVuY3lTZXRcbiAqIEByZXR1cm4ge1N1YmplY3RDb250cm9sbGVyfVxuICovXG5TdWJqZWN0Q29udHJvbGxlci5wcm90b3R5cGUub3IgPSBmdW5jdGlvbihzZXQpIHtcblx0dGhpcy5jb2xsZWN0aW9uLmFkZFNldChuZXcgRGVwZW5kZW5jeVNldChzZXQsIHRoaXMub3B0aW9ucy50cmlnZ2VyKSlcblxuXHRpZiAodGhpcy5jb2xsZWN0aW9uLnF1YWxpZmllZCkge1xuXHRcdHRoaXMuX2VuYWJsZSgpXG5cdH0gZWxzZSB7XG5cdFx0dGhpcy5fZGlzYWJsZSgpXG5cdH1cblxuXHRyZXR1cm4gdGhpc1xufVxuXG4vKipcbiAqIFJ1biBhIGNoZWNrIG9mIHRoZSBjb2xsZWN0aW9uXG4gKi9cblN1YmplY3RDb250cm9sbGVyLnByb3RvdHlwZS5jaGVjayA9IGZ1bmN0aW9uKCkge1xuXHR0aGlzLmNvbGxlY3Rpb24ucnVuQ2hlY2soKVxufVxuXG4vKipcbiAqIEVuYWJsZSB0aGUgc3ViamVjdFxuICogQHBhcmFtICB7RGVwZW5kZW5jeX0gZGVwZW5kZW5jeSBUaGUgdHJpZ2dlcmluZyBkZXBlbmRlbmN5XG4gKiBAcGFyYW0gIHtFdmVudH0gICAgICBlIFRoZSB0cmlnZ2VyaW5nIERPTSBldmVudFxuICogQHByaXZhdGVcbiAqL1xuU3ViamVjdENvbnRyb2xsZXIucHJvdG90eXBlLl9lbmFibGUgPSBmdW5jdGlvbihkZXBlbmRlbmN5LCBlKSB7XG5cdGlmICh0aGlzLm9wdGlvbnMuZGlzYWJsZSkge1xuXHRcdHRoaXMuJHN1YmplY3QuYXR0cignZGlzYWJsZWQnLCBmYWxzZSlcblx0fVxuXG5cdGlmICh0aGlzLm9wdGlvbnMucmVhZG9ubHkpIHtcblx0XHR0aGlzLiRzdWJqZWN0LmF0dHIoJ3JlYWRvbmx5JywgZmFsc2UpXG5cdH1cblxuXHRpZiAodGhpcy5vcHRpb25zLmhpZGUpIHtcblx0XHR0aGlzLl90b2dnbGVEaXNwbGF5KHRydWUsIHRoaXMuaXNJbml0aWFsU3RhdGUpXG5cdH1cblxuXHRpZiAodGhpcy5vcHRpb25zLmhhc093blByb3BlcnR5KCd2YWx1ZU9uRW5hYmxlJykgJiYgdHlwZW9mIHRoaXMub3B0aW9ucy52YWx1ZU9uRW5hYmxlICE9PSB1bmRlZmluZWQpIHtcblx0XHR0aGlzLiR2YWx1ZVRhcmdldC52YWwodGhpcy5vcHRpb25zLnZhbHVlT25FbmFibGUpLmNoYW5nZSgpXG5cdH1cblxuXHRpZiAodGhpcy5vcHRpb25zLmhhc093blByb3BlcnR5KCdjaGVja09uRW5hYmxlJykpIHtcblx0XHR0aGlzLiR2YWx1ZVRhcmdldC5wcm9wKCdjaGVja2VkJywgdGhpcy5vcHRpb25zLmNoZWNrT25FbmFibGUpLmNoYW5nZSgpXG5cdH1cblxuXHRpZiAodGhpcy5vcHRpb25zLmhhc093blByb3BlcnR5KCd0b2dnbGVDbGFzcycpICYmIHR5cGVvZiB0aGlzLm9wdGlvbnMudG9nZ2xlQ2xhc3MgIT09IHVuZGVmaW5lZCkge1xuXHRcdHRoaXMuJHN1YmplY3QuYWRkQ2xhc3ModGhpcy5vcHRpb25zLnRvZ2dsZUNsYXNzKVxuXHR9XG5cblx0dGhpcy5vcHRpb25zLm9uRW5hYmxlLmNhbGwoZGVwZW5kZW5jeSwgZSwgdGhpcy4kc3ViamVjdClcbn1cblxuLyoqXG4gKiBEaXNhYmxlIHRoZSBzdWJqZWN0XG4gKiBAcGFyYW0gIHtEZXBlbmRlbmN5fSBkZXBlbmRlbmN5IFRoZSB0cmlnZ2VyaW5nIGRlcGVuZGVuY3lcbiAqIEBwYXJhbSAge0V2ZW50fSAgICAgIGUgVGhlIHRyaWdnZXJpbmcgRE9NIGV2ZW50XG4gKiBAcHJpdmF0ZVxuICovXG5TdWJqZWN0Q29udHJvbGxlci5wcm90b3R5cGUuX2Rpc2FibGUgPSBmdW5jdGlvbihkZXBlbmRlbmN5LCBlKSB7XG5cdGlmICh0aGlzLm9wdGlvbnMuZGlzYWJsZSkge1xuXHRcdHRoaXMuJHN1YmplY3QuYXR0cignZGlzYWJsZWQnLCB0cnVlKVxuXHR9XG5cblx0aWYgKHRoaXMub3B0aW9ucy5yZWFkb25seSkge1xuXHRcdHRoaXMuJHN1YmplY3QuYXR0cigncmVhZG9ubHknLCB0cnVlKVxuXHR9XG5cblx0aWYgKHRoaXMub3B0aW9ucy5oaWRlKSB7XG5cdFx0dGhpcy5fdG9nZ2xlRGlzcGxheShmYWxzZSwgdGhpcy5pc0luaXRpYWxTdGF0ZSlcblx0fVxuXG5cdGlmICh0aGlzLm9wdGlvbnMuaGFzT3duUHJvcGVydHkoJ3ZhbHVlT25EaXNhYmxlJykgJiYgdHlwZW9mIHRoaXMub3B0aW9ucy52YWx1ZU9uRGlzYWJsZSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0dGhpcy4kdmFsdWVUYXJnZXQudmFsKHRoaXMub3B0aW9ucy52YWx1ZU9uRGlzYWJsZSkuY2hhbmdlKClcblx0fVxuXG5cdGlmICh0aGlzLm9wdGlvbnMuaGFzT3duUHJvcGVydHkoJ2NoZWNrT25EaXNhYmxlJykpIHtcblx0XHR0aGlzLiR2YWx1ZVRhcmdldC5wcm9wKCdjaGVja2VkJywgdGhpcy5vcHRpb25zLmNoZWNrT25EaXNhYmxlKS5jaGFuZ2UoKVxuXHR9XG5cblx0aWYgKHRoaXMub3B0aW9ucy5oYXNPd25Qcm9wZXJ0eSgndG9nZ2xlQ2xhc3MnKSAmJiB0eXBlb2YgdGhpcy5vcHRpb25zLnRvZ2dsZUNsYXNzICE9PSB1bmRlZmluZWQpIHtcblx0XHR0aGlzLiRzdWJqZWN0LnJlbW92ZUNsYXNzKHRoaXMub3B0aW9ucy50b2dnbGVDbGFzcylcblx0fVxuXG5cdHRoaXMub3B0aW9ucy5vbkRpc2FibGUuY2FsbChkZXBlbmRlbmN5LCBlLCB0aGlzLiRzdWJqZWN0KVxufVxuXG4vKipcbiAqIFNob3cgb3IgaGlkZSB0aGUgc3ViamVjdFxuICogQHBhcmFtICB7Qm9vbGVhbn0gc2hvdyAgIFdoZXRoZXIgb3Igbm90IHRvIHNob3cgdGhlIGVsZW1lbnRcbiAqIEBwYXJhbSAge1t0eXBlXX0gIG5vRmFkZSBXaGV0aGVyIG9yIG5vdCB0byBmYWRlIHRoZSBlbGVtZW50XG4gKiBAcHJpdmF0ZVxuICovXG5TdWJqZWN0Q29udHJvbGxlci5wcm90b3R5cGUuX3RvZ2dsZURpc3BsYXkgPSBmdW5jdGlvbihzaG93LCBub0ZhZGUpIHtcblx0dmFyIGlkID0gdGhpcy4kc3ViamVjdC5hdHRyKCdpZCcpXG5cdHZhciAkaGlkZUVsZVxuXG5cdGlmICh0aGlzLiRzdWJqZWN0LnBhcmVudCgpWzBdLm5vZGVOYW1lLnRvTG93ZXJDYXNlKCkgPT09ICdsYWJlbCcpIHtcblx0XHQkaGlkZUVsZSA9IHRoaXMuJHN1YmplY3QucGFyZW50KClcblx0fSBlbHNlIHtcblx0XHQkaGlkZUVsZSA9IHRoaXMuJHN1YmplY3QuYWRkKCdsYWJlbFtmb3I9XCInICsgaWQgKyAnXCJdJylcblx0fVxuXG5cdGlmIChzaG93ICYmICEkaGlkZUVsZS5pcygnOnZpc2libGUnKSkge1xuXHRcdGlmIChub0ZhZGUpIHtcblx0XHRcdCRoaWRlRWxlLnNob3coKVxuXHRcdH0gZWxzZSB7XG5cdFx0XHQkaGlkZUVsZS5mYWRlSW4odGhpcy5vcHRpb25zLmR1cmF0aW9uKVxuXHRcdH1cblx0fSBlbHNlIGlmICghc2hvdykge1xuXHRcdGlmIChub0ZhZGUpIHtcblx0XHRcdCRoaWRlRWxlLmhpZGUoKVxuXHRcdH0gZWxzZSB7XG5cdFx0XHQkaGlkZUVsZS5mYWRlT3V0KHRoaXMub3B0aW9ucy5kdXJhdGlvbilcblx0XHR9XG5cdH1cbn1cblxubW9kdWxlLmV4cG9ydHMgPSBTdWJqZWN0Q29udHJvbGxlclxuXG5cblxuLyoqKioqKioqKioqKioqKioqXG4gKiogV0VCUEFDSyBGT09URVJcbiAqKiAuL3NyYy9zdWJqZWN0LWNvbnRyb2xsZXIuanNcbiAqKiBtb2R1bGUgaWQgPSAxXG4gKiogbW9kdWxlIGNodW5rcyA9IDBcbiAqKi8iLCIvKipcbiAqIERlcGVuZGVuY3lDb2xsZWN0aW9uXG4gKiAtLS1cbiAqIENsYXNzIHdoaWNoIGRlZmluZXMgYSBjb2xsZWN0aW9uIG9mIGRlcGVuZGVuY3kgc2V0cy5cbiAqIEVhY2ggc2V0IGRlZmluZXMgYSBsb2dpY2FsIE9SLCBzbyB0aGUgY29sbGVjdGlvbiBpcyBjb25zaWRlcmVkIHF1YWxpZmllZFxuICogd2hlbiBfYW55XyBvZiB0aGUgc2V0cyBhcmUgcXVhbGlmaWVkLlxuICovXG5cbnZhciBFdmVudEVtaXR0ZXIgID0gcmVxdWlyZSgnZXZlbnRzJykuRXZlbnRFbWl0dGVyXG5cbnZhciBEZXBlbmRlbmN5Q29sbGVjdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHR0aGlzLnNldHMgPSBbXVxuXG5cdC8vIEtlZXAgdHJhY2sgb2YgaG93IG1hbnkgc2V0cyBhcmUgcXVhbGlmaWVkLlxuXHQvLyBRdWFsaWZpZWQgc2V0cyB3aWxsIGFkZCAxLCB1bnF1YWxpZmllZCBzZXRzIHdpbGwgc3VidHJhY3QgMSB1bmxlc3MgdGhlXG5cdC8vIHN1bSBpcyAwLiBUaGUgc3VtIG11c3Qgbm90IGZhbGwgYmVsb3cgemVyby5cblx0dGhpcy5fcXVhbFN1bSA9IDBcblx0dGhpcy5xdWFsaWZpZWQgPSBudWxsXG59XG5cbm1vZHVsZS5leHBvcnRzID0gRGVwZW5kZW5jeUNvbGxlY3Rpb25cblxuRGVwZW5kZW5jeUNvbGxlY3Rpb24ucHJvdG90eXBlID0gJC5leHRlbmQoe30sIEV2ZW50RW1pdHRlci5wcm90b3R5cGUpXG5cbi8qKlxuICogQWRkIGEgZGVwZW5kZW5jeSBzZXQgdG8gdGhlIGNvbGxlY3Rpb25cbiAqIEBwYXJhbSAge0RlcGVuZGVuY3lTZXR9IHNldFxuICovXG5EZXBlbmRlbmN5Q29sbGVjdGlvbi5wcm90b3R5cGUuYWRkU2V0ID0gZnVuY3Rpb24oc2V0KSB7XG5cdHRoaXMuc2V0cy5wdXNoKHNldClcblx0dGhpcy5fcXVhbFN1bSArPSBzZXQucXVhbGlmaWVkID8gMSA6IDBcblx0dGhpcy5xdWFsaWZpZWQgPSB0aGlzLl9xdWFsU3VtID4gMFxuXG5cdHNldC5vbignY2hhbmdlJywgdGhpcy5fc2V0Q2hhbmdlSGFuZGxlci5iaW5kKHRoaXMpKVxufVxuXG4vKipcbiAqIENoZWNrIHRvIHNlZSBpZiB0aGUgY29sbGVjdGlvbiBjYW4gcXVhbGlmeSBieSBjaGVja2luZyBlYWNoIHNldFxuICovXG5EZXBlbmRlbmN5Q29sbGVjdGlvbi5wcm90b3R5cGUucnVuQ2hlY2sgPSBmdW5jdGlvbigpIHtcblx0Zm9yICh2YXIgaSA9IDAsIGxlbiA9IHRoaXMuc2V0cy5sZW5ndGg7IGkgPCBsZW47IGkrKykge1xuXHRcdHRoaXMuc2V0c1tpXS5ydW5DaGVjaygpXG5cdH1cbn1cblxuLyoqXG4gKiBIYW5kbGVyIGZvciBhIHNldCdzIGBjaGFuZ2VgIGV2ZW50XG4gKiBFbWl0IGEgYGNoYW5nZWAgZXZlbnQgd2hlbiB0aGUgcXVhbGZpZWQgc3RhdHVzIG9mIHRoZSBjb2xsZWN0aW9uIGNoYW5nZXNcbiAqIEBwYXJhbSAge09iamVjdH0gc3RhdGVcbiAqIEBwcml2YXRlXG4gKi9cbkRlcGVuZGVuY3lDb2xsZWN0aW9uLnByb3RvdHlwZS5fc2V0Q2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uKHN0YXRlKSB7XG5cdHZhciBwcmV2U3RhdGUgPSB0aGlzLnF1YWxpZmllZFxuXHR0aGlzLl9xdWFsU3VtICs9IHN0YXRlLnF1YWxpZmllZCA/IDEgOiB0aGlzLl9xdWFsU3VtID09PSAwID8gMCA6IC0xXG5cdHRoaXMucXVhbGlmaWVkID0gdGhpcy5fcXVhbFN1bSA+IDBcblx0XG5cdGlmICh0aGlzLnF1YWxpZmllZCAhPT0gcHJldlN0YXRlKSB7XG5cdFx0dGhpcy5lbWl0KCdjaGFuZ2UnLCB7XG5cdFx0XHR0cmlnZ2VyQnk6IHN0YXRlLnRyaWdnZXJCeSxcblx0XHRcdGU6IHN0YXRlLmUsXG5cdFx0XHRxdWFsaWZpZWQ6IHRoaXMucXVhbGlmaWVkXG5cdFx0fSlcblx0fVxufVxuXG5cblxuLyoqKioqKioqKioqKioqKioqXG4gKiogV0VCUEFDSyBGT09URVJcbiAqKiAuL3NyYy9kZXBlbmRlbmN5LWNvbGxlY3Rpb24uanNcbiAqKiBtb2R1bGUgaWQgPSAyXG4gKiogbW9kdWxlIGNodW5rcyA9IDBcbiAqKi8iLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICB0aGlzLl9ldmVudHMgPSB0aGlzLl9ldmVudHMgfHwge307XG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbkV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uKG4pIHtcbiAgaWYgKCFpc051bWJlcihuKSB8fCBuIDwgMCB8fCBpc05hTihuKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ24gbXVzdCBiZSBhIHBvc2l0aXZlIG51bWJlcicpO1xuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIGVyLCBoYW5kbGVyLCBsZW4sIGFyZ3MsIGksIGxpc3RlbmVycztcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmICh0eXBlID09PSAnZXJyb3InKSB7XG4gICAgaWYgKCF0aGlzLl9ldmVudHMuZXJyb3IgfHxcbiAgICAgICAgKGlzT2JqZWN0KHRoaXMuX2V2ZW50cy5lcnJvcikgJiYgIXRoaXMuX2V2ZW50cy5lcnJvci5sZW5ndGgpKSB7XG4gICAgICBlciA9IGFyZ3VtZW50c1sxXTtcbiAgICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgICAgfVxuICAgICAgdGhyb3cgVHlwZUVycm9yKCdVbmNhdWdodCwgdW5zcGVjaWZpZWQgXCJlcnJvclwiIGV2ZW50LicpO1xuICAgIH1cbiAgfVxuXG4gIGhhbmRsZXIgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgaWYgKGlzVW5kZWZpbmVkKGhhbmRsZXIpKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAoaXNGdW5jdGlvbihoYW5kbGVyKSkge1xuICAgIHN3aXRjaCAoYXJndW1lbnRzLmxlbmd0aCkge1xuICAgICAgLy8gZmFzdCBjYXNlc1xuICAgICAgY2FzZSAxOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcyk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAyOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcywgYXJndW1lbnRzWzFdKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIDM6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzLCBhcmd1bWVudHNbMV0sIGFyZ3VtZW50c1syXSk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgLy8gc2xvd2VyXG4gICAgICBkZWZhdWx0OlxuICAgICAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICAgICAgaGFuZGxlci5hcHBseSh0aGlzLCBhcmdzKTtcbiAgICB9XG4gIH0gZWxzZSBpZiAoaXNPYmplY3QoaGFuZGxlcikpIHtcbiAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICBsaXN0ZW5lcnMgPSBoYW5kbGVyLnNsaWNlKCk7XG4gICAgbGVuID0gbGlzdGVuZXJzLmxlbmd0aDtcbiAgICBmb3IgKGkgPSAwOyBpIDwgbGVuOyBpKyspXG4gICAgICBsaXN0ZW5lcnNbaV0uYXBwbHkodGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgbTtcblxuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgdGhpcy5fZXZlbnRzID0ge307XG5cbiAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICBpZiAodGhpcy5fZXZlbnRzLm5ld0xpc3RlbmVyKVxuICAgIHRoaXMuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICBpc0Z1bmN0aW9uKGxpc3RlbmVyLmxpc3RlbmVyKSA/XG4gICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICBlbHNlIGlmIChpc09iamVjdCh0aGlzLl9ldmVudHNbdHlwZV0pKVxuICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0ucHVzaChsaXN0ZW5lcik7XG4gIGVsc2VcbiAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0gPSBbdGhpcy5fZXZlbnRzW3R5cGVdLCBsaXN0ZW5lcl07XG5cbiAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgaWYgKGlzT2JqZWN0KHRoaXMuX2V2ZW50c1t0eXBlXSkgJiYgIXRoaXMuX2V2ZW50c1t0eXBlXS53YXJuZWQpIHtcbiAgICBpZiAoIWlzVW5kZWZpbmVkKHRoaXMuX21heExpc3RlbmVycykpIHtcbiAgICAgIG0gPSB0aGlzLl9tYXhMaXN0ZW5lcnM7XG4gICAgfSBlbHNlIHtcbiAgICAgIG0gPSBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgICB9XG5cbiAgICBpZiAobSAmJiBtID4gMCAmJiB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoID4gbSkge1xuICAgICAgdGhpcy5fZXZlbnRzW3R5cGVdLndhcm5lZCA9IHRydWU7XG4gICAgICBjb25zb2xlLmVycm9yKCcobm9kZSkgd2FybmluZzogcG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSAnICtcbiAgICAgICAgICAgICAgICAgICAgJ2xlYWsgZGV0ZWN0ZWQuICVkIGxpc3RlbmVycyBhZGRlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICdVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byBpbmNyZWFzZSBsaW1pdC4nLFxuICAgICAgICAgICAgICAgICAgICB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoKTtcbiAgICAgIGlmICh0eXBlb2YgY29uc29sZS50cmFjZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAvLyBub3Qgc3VwcG9ydGVkIGluIElFIDEwXG4gICAgICAgIGNvbnNvbGUudHJhY2UoKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgdmFyIGZpcmVkID0gZmFsc2U7XG5cbiAgZnVuY3Rpb24gZygpIHtcbiAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGcpO1xuXG4gICAgaWYgKCFmaXJlZCkge1xuICAgICAgZmlyZWQgPSB0cnVlO1xuICAgICAgbGlzdGVuZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH1cblxuICBnLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHRoaXMub24odHlwZSwgZyk7XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG4vLyBlbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWZmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZFxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBsaXN0LCBwb3NpdGlvbiwgbGVuZ3RoLCBpO1xuXG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldHVybiB0aGlzO1xuXG4gIGxpc3QgPSB0aGlzLl9ldmVudHNbdHlwZV07XG4gIGxlbmd0aCA9IGxpc3QubGVuZ3RoO1xuICBwb3NpdGlvbiA9IC0xO1xuXG4gIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fFxuICAgICAgKGlzRnVuY3Rpb24obGlzdC5saXN0ZW5lcikgJiYgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICBpZiAodGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3RlbmVyKTtcblxuICB9IGVsc2UgaWYgKGlzT2JqZWN0KGxpc3QpKSB7XG4gICAgZm9yIChpID0gbGVuZ3RoOyBpLS0gPiAwOykge1xuICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8XG4gICAgICAgICAgKGxpc3RbaV0ubGlzdGVuZXIgJiYgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKSB7XG4gICAgICBsaXN0Lmxlbmd0aCA9IDA7XG4gICAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIH0gZWxzZSB7XG4gICAgICBsaXN0LnNwbGljZShwb3NpdGlvbiwgMSk7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0ZW5lcik7XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIga2V5LCBsaXN0ZW5lcnM7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgcmV0dXJuIHRoaXM7XG5cbiAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICBpZiAoIXRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcikge1xuICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKVxuICAgICAgdGhpcy5fZXZlbnRzID0ge307XG4gICAgZWxzZSBpZiAodGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgIGZvciAoa2V5IGluIHRoaXMuX2V2ZW50cykge1xuICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgIH1cbiAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGxpc3RlbmVycyA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICBpZiAoaXNGdW5jdGlvbihsaXN0ZW5lcnMpKSB7XG4gICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICB9IGVsc2UgaWYgKGxpc3RlbmVycykge1xuICAgIC8vIExJRk8gb3JkZXJcbiAgICB3aGlsZSAobGlzdGVuZXJzLmxlbmd0aClcbiAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2xpc3RlbmVycy5sZW5ndGggLSAxXSk7XG4gIH1cbiAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIgcmV0O1xuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldCA9IFtdO1xuICBlbHNlIGlmIChpc0Z1bmN0aW9uKHRoaXMuX2V2ZW50c1t0eXBlXSkpXG4gICAgcmV0ID0gW3RoaXMuX2V2ZW50c1t0eXBlXV07XG4gIGVsc2VcbiAgICByZXQgPSB0aGlzLl9ldmVudHNbdHlwZV0uc2xpY2UoKTtcbiAgcmV0dXJuIHJldDtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgaWYgKHRoaXMuX2V2ZW50cykge1xuICAgIHZhciBldmxpc3RlbmVyID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKGlzRnVuY3Rpb24oZXZsaXN0ZW5lcikpXG4gICAgICByZXR1cm4gMTtcbiAgICBlbHNlIGlmIChldmxpc3RlbmVyKVxuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICB9XG4gIHJldHVybiAwO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG59O1xuXG5mdW5jdGlvbiBpc0Z1bmN0aW9uKGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ2Z1bmN0aW9uJztcbn1cblxuZnVuY3Rpb24gaXNOdW1iZXIoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnbnVtYmVyJztcbn1cblxuZnVuY3Rpb24gaXNPYmplY3QoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnb2JqZWN0JyAmJiBhcmcgIT09IG51bGw7XG59XG5cbmZ1bmN0aW9uIGlzVW5kZWZpbmVkKGFyZykge1xuICByZXR1cm4gYXJnID09PSB2b2lkIDA7XG59XG5cblxuXG4vKioqKioqKioqKioqKioqKipcbiAqKiBXRUJQQUNLIEZPT1RFUlxuICoqIC4vfi9ldmVudHMvZXZlbnRzLmpzXG4gKiogbW9kdWxlIGlkID0gM1xuICoqIG1vZHVsZSBjaHVua3MgPSAwXG4gKiovIiwiLyoqXG4gKiBEZXBlbmRlbmN5U2V0XG4gKiAtLS1cbiAqIENsYXNzIHdoaWNoIGRlZmluZXMgYSBzZXQgb2YgZGVwZW5kZW5jaWVzXG4gKi9cblxudmFyIEV2ZW50RW1pdHRlciA9IHJlcXVpcmUoJ2V2ZW50cycpLkV2ZW50RW1pdHRlclxudmFyIERlcGVuZGVuY3kgICA9IHJlcXVpcmUoJy4vZGVwZW5kZW5jeScpXG5cbnZhciBEZXBlbmRlbmN5U2V0ID0gZnVuY3Rpb24oZGVwZW5kZW5jaWVzLCB0cmlnZ2VyKSB7XG5cdHRoaXMuZGVwZW5kZW5jaWVzID0gW11cblxuXHQvLyBLZWVwIHRyYWNrIG9mIGhvdyBtYW55IGRlcGVuZGVuY2llcyBhcmUgcXVhbGlmaWVkLlxuXHQvLyBRdWFsaWZpZWQgZGVwZW5kZW5jaWVzIHdpbGwgYWRkIDEsIHVucXVhbGlmaWVkIGRlcGVuZGVuY2llcyB3aWxsXG5cdC8vIHN1YnRyYWN0IDEgdW5sZXNzIHRoZSBzdW0gaXMgMC4gVGhlIHN1bSBtdXN0IG5vdCBmYWxsIGJlbG93IHplcm8uXG5cdHZhciBxdWFsU3VtID0gMFxuXG5cdGZvciAodmFyIGQgaW4gZGVwZW5kZW5jaWVzKSB7XG5cdFx0aWYgKCFkZXBlbmRlbmNpZXMuaGFzT3duUHJvcGVydHkoZCkpIGNvbnRpbnVlXG5cblx0XHR2YXIgbmV3RGVwID0gbmV3IERlcGVuZGVuY3koZCwgZGVwZW5kZW5jaWVzW2RdLCB0cmlnZ2VyKVxuXHRcdHRoaXMuZGVwZW5kZW5jaWVzLnB1c2gobmV3RGVwKVxuXHRcdHF1YWxTdW0gKz0gbmV3RGVwLnF1YWxpZmllZCA/IDEgOiAwXG5cblx0XHRuZXdEZXAub24oJ2NoYW5nZScsIGdldERlcENoYW5nZUhhbmRsZXIobmV3RGVwKS5iaW5kKHRoaXMpKVxuXHR9XG5cblx0dGhpcy5kb2VzUXVhbGlmeSA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBxdWFsU3VtID09PSB0aGlzLmRlcGVuZGVuY2llcy5sZW5ndGhcblx0fVxuXG5cdC8vIFNldCBpbml0aWFsIHN0YXRlIG9mIHRoZSBzZXRcblx0dGhpcy5xdWFsaWZpZWQgPSB0aGlzLmRvZXNRdWFsaWZ5KClcblxuXHRmdW5jdGlvbiBnZXREZXBDaGFuZ2VIYW5kbGVyKGRlcCkge1xuXHRcdHJldHVybiBmdW5jdGlvbihzdGF0ZSkge1xuXHRcdFx0dmFyIHByZXZTdGF0ZSA9IHRoaXMucXVhbGlmaWVkXG5cdFx0XHRxdWFsU3VtICs9IHN0YXRlLnF1YWxpZmllZCA/IDEgOiBxdWFsU3VtID09PSAwID8gMCA6IC0xXG5cdFx0XHR0aGlzLnF1YWxpZmllZCA9IHRoaXMuZG9lc1F1YWxpZnkoKVxuXG5cdFx0XHRpZiAodGhpcy5xdWFsaWZpZWQgIT09IHByZXZTdGF0ZSkge1xuXHRcdFx0XHR0aGlzLmVtaXQoJ2NoYW5nZScsIHtcblx0XHRcdFx0XHR0cmlnZ2VyQnk6IGRlcCxcblx0XHRcdFx0XHRlOiBzdGF0ZS5lLFxuXHRcdFx0XHRcdHF1YWxpZmllZDogdGhpcy5kb2VzUXVhbGlmeSgpXG5cdFx0XHRcdH0pXG5cdFx0XHR9XG5cdFx0fVxuXHR9XG59XG5cbm1vZHVsZS5leHBvcnRzID0gRGVwZW5kZW5jeVNldFxuXG5EZXBlbmRlbmN5U2V0LnByb3RvdHlwZSA9ICQuZXh0ZW5kKHt9LCBFdmVudEVtaXR0ZXIucHJvdG90eXBlKVxuXG4vKipcbiAqIFJ1biBhIHF1YWxpZmljYXRpb24gY2hlY2sgb2YgYWxsIGRlcGVuZGVuY2llc1xuICovXG5EZXBlbmRlbmN5U2V0LnByb3RvdHlwZS5ydW5DaGVjayA9IGZ1bmN0aW9uKCkge1xuXHRmb3IgKHZhciBpID0gMCwgbGVuID0gdGhpcy5kZXBlbmRlbmNpZXMubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcblx0XHR0aGlzLmRlcGVuZGVuY2llc1tpXS5ydW5DaGVjaygpXG5cdH1cbn1cblxuXG5cbi8qKioqKioqKioqKioqKioqKlxuICoqIFdFQlBBQ0sgRk9PVEVSXG4gKiogLi9zcmMvZGVwZW5kZW5jeS1zZXQuanNcbiAqKiBtb2R1bGUgaWQgPSA0XG4gKiogbW9kdWxlIGNodW5rcyA9IDBcbiAqKi8iLCIvKipcbiAqIERlcGVuZGVuY3lcbiAqIC0tLVxuICogQ2xhc3Mgd2hpY2ggZGVmaW5lcyBkZXBlbmRlbmN5IHF1YWxpZmllcnNcbiAqL1xuXG5cbnZhciBFdmVudEVtaXR0ZXIgPSByZXF1aXJlKCdldmVudHMnKS5FdmVudEVtaXR0ZXJcblxudmFyIERlcGVuZGVuY3kgPSBmdW5jdGlvbihzZWxlY3RvciwgcXVhbGlmaWVycywgdHJpZ2dlcikge1xuXHR0aGlzLiRlbGUgPSAkKHNlbGVjdG9yKVxuXHR0aGlzLnF1YWxpZmllcnMgPSBxdWFsaWZpZXJzXG5cdHRoaXMuZmllbGRTdGF0ZSA9IGdldEZpZWxkU3RhdGUodGhpcy4kZWxlKVxuXHR0aGlzLm1ldGhvZHMgPSBbXG5cdFx0J2VuYWJsZWQnLFxuXHRcdCdjaGVja2VkJyxcblx0XHQndmFsdWVzJyxcblx0XHQnbm90Jyxcblx0XHQnbWF0Y2gnLFxuXHRcdCdjb250YWlucycsXG5cdFx0J2VtYWlsJyxcblx0XHQndXJsJ1xuXHRdXG5cblx0Ly8gU2V0IGluaXRpYWwgc3RhdGUgb2YgdGhlIGRlcGVuZGVuY3lcblx0dGhpcy5xdWFsaWZpZWQgPSB0aGlzLmRvZXNRdWFsaWZ5KClcblxuXHR0aGlzLiRlbGUub24odHJpZ2dlciwgaGFuZGxlci5iaW5kKHRoaXMpKVxuXHR0aGlzLnJ1bkNoZWNrID0gaGFuZGxlci5iaW5kKHRoaXMpXG5cblx0ZnVuY3Rpb24gaGFuZGxlcihlKSB7XG5cdFx0dmFyIHByZXZTdGF0ZSA9IHRoaXMucXVhbGlmaWVkXG5cblx0XHR0aGlzLmZpZWxkU3RhdGUgPSBnZXRGaWVsZFN0YXRlKHRoaXMuJGVsZSlcblx0XHR0aGlzLnF1YWxpZmllZCA9IHRoaXMuZG9lc1F1YWxpZnkoKVxuXG5cdFx0aWYgKHRoaXMucXVhbGlmaWVkICE9PSBwcmV2U3RhdGUpIHtcblx0XHRcdHRoaXMuZW1pdCgnY2hhbmdlJywge1xuXHRcdFx0XHRzZWxlY3Rvcjogc2VsZWN0b3IsXG5cdFx0XHRcdGU6IGUsXG5cdFx0XHRcdHF1YWxpZmllZDogdGhpcy5xdWFsaWZpZWRcblx0XHRcdH0pXG5cdFx0fVxuXHR9XG59XG5cbkRlcGVuZGVuY3kucHJvdG90eXBlID0gJC5leHRlbmQoe30sIEV2ZW50RW1pdHRlci5wcm90b3R5cGUpXG5cbi8qKlxuICogUXVhbGlmaWVyIG1ldGhvZCB3aGljaCBjaGVja3MgZm9yIHRoZSBgZGlzYWJsZWRgIGF0dHJpYnV0ZS5cbiAqIC0tLVxuICogUmV0dXJucyBmYWxzZSB3aGVuIGRlcGVuZGVuY3kgaXMgZGlzYWJsZWQgYW5kIGBlbmFibGVkYFxuICogcXVhbGlmaWVyIGlzIHRydWUgKm9yKiB3aGVuIGRlcGVuZGVuY3kgaXMgbm90IGRpc2FibGVkIGFuZFxuICogYGVuYWJsZWRgIHF1YWxpZmllciBpcyBmYWxzZS5cbiAqIFJldHVybnMgdHJ1ZSBvdGhlcndpc2UuXG4gKlxuICogQHBhcmFtIHtCb29sZWFufSBlbmFibGVkVmFsIFRoZSB2YWx1ZSB3ZSBhcmUgY2hlY2tpbmdcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLmVuYWJsZWQgPSBmdW5jdGlvbihlbmFibGVkVmFsKSB7XG5cdGlmICgoIXRoaXMuZmllbGRTdGF0ZS5kaXNhYmxlZCAmJiBlbmFibGVkVmFsKSB8fFxuXHRcdCh0aGlzLmZpZWxkU3RhdGUuZGlzYWJsZWQgJiYgIWVuYWJsZWRWYWwpKSB7XG5cdFx0cmV0dXJuIHRydWVcblx0fVxuXG5cdHJldHVybiBmYWxzZVxufVxuXG4vKipcbiAqIFF1YWxpZmllciBtZXRob2Qgd2hpY2ggY2hlY2tzIGZvciB0aGUgYGNoZWNrZWRgIGF0dHJpYnV0ZSBvblxuICogY2hlY2tib3hlcyBhbmQgcmFkaW8gYnV0dG9ucy5cbiAqIC0tLVxuICogRGVwZW5kZW5jeSBtdXN0IGJlIGEgY2hlY2tib3ggZm9yIHRoaXMgcXVhbGlmaWVyLlxuICogUmV0dXJucyBmYWxzZSBpZiBjaGVja2JveCBpcyBub3QgY2hlY2tlZCBhbmQgdGhlIGBjaGVja2VkYCBxdWFsaWZpZXJcbiAqIGlzIHRydWUgKm9yKiB0aGUgY2hlY2tib3ggaXMgY2hlY2tlZCBhbmQgdGhlIGBjaGVja2VkYCBxdWFsaWZpZXJcbiAqIGlzIGZhbHNlLlxuICpcbiAqIEBwYXJhbSB7Qm9vbGVhbn0gY2hlY2tlZFZhbCBUaGUgdmFsdWUgd2UgYXJlIGNoZWNraW5nLlxuICogQHJldHVybiB7Qm9vbGVhbn1cbiAqL1xuRGVwZW5kZW5jeS5wcm90b3R5cGUuY2hlY2tlZCA9IGZ1bmN0aW9uKGNoZWNrZWRWYWwpIHtcblx0aWYgKHRoaXMuJGVsZS5hdHRyKCd0eXBlJykgPT09ICdjaGVja2JveCcpIHtcblx0XHRpZiAoKCF0aGlzLmZpZWxkU3RhdGUuY2hlY2tlZCAmJiBjaGVja2VkVmFsKSB8fFxuXHRcdFx0KHRoaXMuZmllbGRTdGF0ZS5jaGVja2VkICYmICFjaGVja2VkVmFsKSkge1xuXHRcdFx0cmV0dXJuIGZhbHNlXG5cdFx0fVxuXHR9XG5cblx0cmV0dXJuIHRydWVcbn1cblxuLyoqXG4gKiBRdWFsaWZpZXIgbWV0aG9kIHdoaWNoIGNoZWNrcyB0aGUgZGVwZW5kZW5jeSBpbnB1dCB2YWx1ZSBhZ2FpbnN0IGFuXG4gKiBhcnJheSBvZiB3aGl0ZWxpc3RlZCB2YWx1ZXMuXG4gKiAtLS1cbiAqIEZvciBzaW5nbGUgdmFsdWUgZGVwZW5kZW5jeSwgcmV0dXJucyB0cnVlIGlmIHZhbHVlcyBtYXRjaC5cbiAqIFdoZW4gZGVwZW5kZW5jeSB2YWx1ZSBpcyBhbiBhcnJheSwgcmV0dXJucyB0cnVlIGlmIGFycmF5IGNvbXBhcmVzIHRvXG4gKiBhIHNpbmdsZSB2YWx1ZSBpbiB0aGUgd2hpdGxpc3QuXG4gKiBUaGlzIGlzIGhlbHBmdWwgd2hlbiBkZWFsaW5nIHdpdGggYSBtdWx0aXNlbGVjdCBpbnB1dCwgYW5kIGNvbXBhcmluZ1xuICogYWdhaW5zdCBhbiBhcnJheSBvZiB2YWx1ZSBzZXRzOlxuICogXHRcdFsgWzEsIDIsIDNdLCBbNCwgNSwgNl0sIFs3LCA4XSBdXG4gKlxuICogQHBhcmFtICB7QXJyYXl9IHdoaXRlbGlzdCBUaGUgbGlzdCBvZiBhY2NlcHRhYmxlIHZhbHVlc1xuICogQHJldHVybiB7Qm9vbGVhbn1cbiAqL1xuRGVwZW5kZW5jeS5wcm90b3R5cGUudmFsdWVzID0gZnVuY3Rpb24od2hpdGVsaXN0KSB7XG5cdGZvciAodmFyIGkgPSAwLCBsZW4gPSB3aGl0ZWxpc3QubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcblx0XHRpZiAodGhpcy5maWVsZFN0YXRlLnZhbHVlICE9PSBudWxsICYmIEFycmF5LmlzQXJyYXkodGhpcy5maWVsZFN0YXRlLnZhbHVlKSkge1xuXHRcdFx0aWYgKCQodGhpcy5maWVsZFN0YXRlLnZhbHVlKS5ub3Qod2hpdGVsaXN0W2ldKS5sZW5ndGggPT09IDAgJiZcblx0XHRcdFx0JCh3aGl0ZWxpc3RbaV0pLm5vdCh0aGlzLmZpZWxkU3RhdGUudmFsdWUpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRyZXR1cm4gdHJ1ZVxuXHRcdFx0fVxuXHRcdH0gZWxzZSBpZiAod2hpdGVsaXN0W2ldID09PSB0aGlzLmZpZWxkU3RhdGUudmFsdWUpIHtcblx0XHRcdHJldHVybiB0cnVlXG5cdFx0fVxuXHR9XG5cblx0cmV0dXJuIGZhbHNlXG59XG5cbi8qKlxuICogUXVhbGlmaWVyIG1ldGhvZCB3aGljaCBjaGVja3MgdGhlIGRlcGVuZGVuY3kgaW5wdXQgdmFsdWUgYWdhaW5zdCBhblxuICogYXJyYXkgb2YgYmxhY2tsaXN0ZWQgdmFsdWVzLlxuICogLS0tXG4gKiBSZXR1cm5zIHRydWUgd2hlbiB0aGUgZGVwZW5kZW5jeSB2YWx1ZSBpcyBub3QgaW4gdGhlIGJsYWNrbGlzdC5cbiAqXG4gKiBAcGFyYW0gIHtBcnJheX0gYmxhY2tsaXN0IFRoZSBsaXN0IG9mIHVuYWNjZXB0YWJsZSB2YWx1ZXNcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLm5vdCA9IGZ1bmN0aW9uKGJsYWNrbGlzdCkge1xuXHRyZXR1cm4gIXRoaXMudmFsdWVzKGJsYWNrbGlzdClcbn1cblxuLyoqXG4gKiBRdWFsaWZpZXIgbWV0aG9kIHdoaWNoIHJ1bnMgYSBSZWdFeCBwYXR0ZXJuIG1hdGNoIG9uIHRoZSBkZXBlbmRlbmN5XG4gKiBpbnB1dCB2YWx1ZS5cbiAqIC0tLVxuICogUmV0dXJucyB0cnVlIHdoZW4gdGhlIGRlcGVuZGVuY3kgdmFsdWUgbWF0Y2hlcyB0aGUgcmVndWxhciBleHByZXNzaW9uLlxuICogSWYgZGVwZW5kZW5jeSB2YWx1ZSBpcyBhbiBhcnJheSwgd2lsbCBvbmx5IHJldHVybiB0cnVlIGlmIF9hbGxfIHZhbHVlc1xuICogbWF0Y2ggdGhlIHJlZ3VsYXIgZXhwcmVzc2lvbi5cbiAqXG4gKiBAcGFyYW0gIHtSZWdFeHB9IHJlZ2V4IFJlZ3VsYXIgZXhwcmVzc2lvbiB0byB0ZXN0IGFnYWluc3RcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLm1hdGNoID0gZnVuY3Rpb24ocmVnZXgpIHtcblx0dmFyIHZhbCA9IHRoaXMuZmllbGRTdGF0ZS52YWx1ZVxuXG5cdGlmICghQXJyYXkuaXNBcnJheSh0aGlzLmZpZWxkU3RhdGUudmFsdWUpKSB7XG5cdFx0dmFsID0gW3ZhbF1cblx0fVxuXG5cdGZvciAodmFyIGkgPSAwLCBsZW4gPSB2YWwubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcblx0XHRpZiAoIXJlZ2V4LnRlc3QodmFsW2ldKSkgcmV0dXJuIGZhbHNlXG5cdH1cblxuXHRyZXR1cm4gdHJ1ZVxufVxuXG4vKipcbiAqIFF1YWxpZmllciBtZXRob2Qgd2hpY2ggcnVucyBhIFJlZ0V4cCBwYXR0ZXJuIG1hdGNoIG9uIHRoZSBkZXBlbmRlbmN5XG4gKiBpbnB1dCB2YWx1ZS5cbiAqIC0tLVxuICogUmV0dXJucyB0cnVlIHdoZW4gdGhlIGRlcGVuZGVuY3kgdmFsdWUgZG9lcyAqbm90KiBtYXRjaCB0aGUgcmVnZXhwLlxuICogSWYgZGVwZW5kZW5jeSB2YWx1ZSBpcyBhbiBhcnJheSwgd2lsbCBvbmx5IHJldHVybiB0cnVlIGlmIF9ub25lXyBvZiB0aGVcbiAqIHZhbHVlcyBtYXRjaCB0aGUgcmVndWxhciBleHByZXNzaW9uLlxuICpcbiAqIEBwYXJhbSAge1JlZ0V4cH0gcmVnZXggUmVndWxhciBleHByZXNzaW9uIHRvIHRlc3QgYWdhaW5zdFxuICogQHJldHVybiB7Qm9vbGVhbn1cbiAqL1xuRGVwZW5kZW5jeS5wcm90b3R5cGUubm90TWF0Y2ggPSBmdW5jdGlvbihyZWdleCkge1xuXHR2YXIgdmFsID0gdGhpcy5maWVsZFN0YXRlLnZhbHVlXG5cblx0aWYgKCFBcnJheS5pc0FycmF5KHRoaXMuZmllbGRTdGF0ZS52YWx1ZSkpIHtcblx0XHR2YWwgPSBbdmFsXVxuXHR9XG5cblx0Zm9yICh2YXIgaSA9IDAsIGxlbiA9IHZhbC5sZW5ndGg7IGkgPCBsZW47IGkrKykge1xuXHRcdGlmIChyZWdleC50ZXN0KHZhbFtpXSkpIHJldHVybiBmYWxzZVxuXHR9XG5cblx0cmV0dXJuIHRydWVcbn1cblxuLyoqXG4gKiBRdWFsaWZpZXIgbWV0aG9kIHdoaWNoIGNoZWNrcyBpZiBhIHdoaXRlbGlzdGVkIHZhbHVlIGV4aXN0cyBpbiBhblxuICogYXJyYXkgb2YgZGVwZW5kZW5jeSB2YWx1ZXMuXG4gKiAtLS1cbiAqIFVzZWZ1bCBmb3Igc2VsZWN0IGlucHV0cyB3aXRoIHRoZSBgbXVsdGlwbGVgIGF0dHJpYnV0ZS5cbiAqIElmIGRlcGVuZGVuY3kgdmFsdWUgaXMgbm90IGFuIGFycmF5LCB0aGUgbWV0aG9kIHdpbGwgZmFsbGJhY2sgdG8gdGhlXG4gKiBgdmFsdWVzYCBxdWFsaWZpZXIuXG4gKlxuICogQHBhcmFtICB7QXJyYXl9IHdoaXRlbGlzdCBMaXN0IG9mIGFjY2VwdGFibGUgdmFsdWVzXG4gKiBAcmV0dXJuIHtCb29sZWFufVxuICovXG5EZXBlbmRlbmN5LnByb3RvdHlwZS5jb250YWlucyA9IGZ1bmN0aW9uKHdoaXRlbGlzdCkge1xuXHRpZiAoIUFycmF5LmlzQXJyYXkodGhpcy5maWVsZFN0YXRlLnZhbHVlKSkge1xuXHRcdHJldHVybiB0aGlzLnZhbHVlcyh3aGl0ZWxpc3QpXG5cdH1cblxuXHRmb3IgKHZhciBpID0gMCwgbGVuID0gd2hpdGVsaXN0Lmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG5cdFx0aWYgKCQuaW5BcnJheSh3aGl0ZWxpc3RbaV0sIHRoaXMuZmllbGRTdGF0ZS52YWx1ZSkgIT09IC0xKSB7XG5cdFx0XHRyZXR1cm4gdHJ1ZVxuXHRcdH1cblx0fVxuXG5cdHJldHVybiBmYWxzZVxufVxuXG4vKipcbiAqIFF1YWxpZmllciBtZXRob2Qgd2hpY2ggY2hlY2tzIHRoYXQgdGhlIHZhbHVlIGlzIGEgdmFsaWQgZW1haWwgYWRkcmVzc1xuICogLS0tXG4gKiBSZXR1cm5zIHRydWUgd2hlbiB0aGUgdmFsdWUgaXMgYW4gZW1haWwgYWRkcmVzcyBhbmQgYHNob3VsZE1hdGNoYCBpc1xuICogdHJ1ZSAqb3IqIHdoZW4gdmFsdWUgaXMgbm90IGFuIGVtYWlsIGFkZHJlc3MgYW5kIGBzaG91bGRNYXRjaGBcbiAqIGlzIGZhbHNlLlxuICpcbiAqIEBwYXJhbSAge0Jvb2xlYW59IHNob3VsZE1hdGNoIFNob3VsZCB0aGUgdmFsdWUgYmUgdmFsaWRcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLmVtYWlsID0gZnVuY3Rpb24oc2hvdWxkTWF0Y2gpIHtcblx0dmFyIHJlZ2V4ID0gL15bX2EtekEtWjAtOVxcLVxcK10rKFxcLltfYS16QS1aMC05XFwtXFwrXSspKkBbYS16QS1aMC05XFwtXSsoXFwuW2EtekEtWjAtOVxcLV0rKSpcXC4oKFswLTldezEsM30pfChbYS16QS1aXXsyLDN9KXwoYWVyb3xjb29wfGluZm98bXVzZXVtfG5hbWUpKSQvXG5cblx0cmV0dXJuIHRoaXMubWF0Y2gocmVnZXgpID09PSBzaG91bGRNYXRjaFxufVxuXG4vKipcbiAqIFF1YWxpZmllciBtZXRob2Qgd2hpY2ggY2hlY2tzIHRoYXQgdGhlIHZhbHVlIGlzIGEgdmFsaWQgVVJMXG4gKiAtLS1cbiAqIFJldHVybnMgdHJ1ZSB3aGVuIHRoZSB2YWx1ZSBpcyBhIFVSTCBhbmQgYHNob3VsZE1hdGNoYCBpcyB0cnVlICpvcipcbiAqIHdoZW4gdmFsdWUgaXMgbm90IGEgVVJMIGFuZCBgc2hvdWxkTWF0Y2hgIGlzIGZhbHNlLlxuICpcbiAqIEBwYXJhbSAge0Jvb2xlYW59IHNob3VsZE1hdGNoIFNob3VsZCB0aGUgdmFsdWUgYmUgdmFsaWRcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLnVybCA9IGZ1bmN0aW9uKHNob3VsZE1hdGNoKSB7XG5cdHZhciByZWdleCA9IC8oKChodHRwfGZ0cHxodHRwcyk6XFwvXFwvKXx3d3dcXC4pW1xcd1xcLV9dKyhcXC5bXFx3XFwtX10rKSsoW1xcd1xcLVxcLixAP1xcXj0lJjpcXC9+XFwrIyFdKltcXHdcXC1cXEA/XFxePSUmXFwvflxcKyNdKT8vXG5cblx0cmV0dXJuIHRoaXMubWF0Y2gocmVnZXgpID09PSBzaG91bGRNYXRjaFxufVxuXG4vKipcbiAqIFF1YWxpZmllciBtZXRob2Qgd2hpY2ggY2hlY2tzIHRoYXQgdGhlIHZhbHVlIHdpdGhpbiBhbiBpbmNsdXNpdmVcbiAqIG51bWVyaWNhbCByYW5nZVxuICogLS0tXG4gKiBSZXR1cm5zIHRydWUgd2hlbiB0aGUgdmFsdWUgZmFsbHMgd2l0aGluIHRoZSByYW5nZS4gQWxwaGEgY2hhcmFjdGVycyBjYW5cbiAqIGFsc28gYmUgZXZhbHVhdGVkLCBhbmQgd2lsbCBvbmx5IGJlIGNvbnNpZGVyZWQgdmFsaWQgd2hlbiB0aGUgcmFuZ2UgdmFsdWVzXG4gKiBhcmUgYWxzbyBhcGhhIGNoYXJhY3RlcnMuXG4gKlxuICogQHBhcmFtICB7TnVtYmVyfENoYXJhY3Rlcn0gc3RhcnQgVGhlIHJhbmdlIHN0YXJ0XG4gKiBAcGFyYW0gIHtOdW1iZXJ8Q2hhcmFjdGVyfSBlbmQgVGhlIHJhbmdlIGV4dGVuZFxuICogQHBhcmFtICB7TnVtYmVyfSAgICAgICAgICAgW3N0ZXBdIFRoZSBudW1iZXIgb2Ygc3RlcHNcbiAqIEByZXR1cm4ge0Jvb2xlYW59XG4gKi9cbkRlcGVuZGVuY3kucHJvdG90eXBlLnJhbmdlID0gZnVuY3Rpb24oc3RhcnQsIGVuZCwgc3RlcCkge1xuXHR2YXIgdHlwZSA9IHR5cGVvZiBzdGFydCA9PT0gJ3N0cmluZycgPyAnY2hhcicgOiAnbnVtYmVyJ1xuXHR2YXIgc3RhcnRWYWwgPSB0eXBlID09PSAnY2hhcicgPyBzdGFydC5jaGFyQ29kZUF0KCkgOiBzdGFydFxuXHR2YXIgZW5kVmFsID0gdHlwZSA9PT0gJ2NoYXInID8gZW5kLmNoYXJDb2RlQXQoKSA6IGVuZFxuXHR2YXIgdmFsID0gdHlwZSA9PT0gJ2NoYXInID8gdGhpcy5maWVsZFN0YXRlLnZhbHVlLmNoYXJDb2RlQXQoKSA6IHBhcnNlRmxvYXQodGhpcy5maWVsZFN0YXRlLnZhbHVlKVxuXG5cdGlmIChzdGVwKSB7XG5cdFx0dmFyIHZhbEFycmF5ID0gW11cblx0XHRmb3IgKHZhciBpID0gc3RhcnRWYWw7IGkgPD0gZW5kVmFsOyBpICs9IHN0ZXApIHZhbEFycmF5LnB1c2goaSlcblx0XHRyZXR1cm4gdmFsQXJyYXkuaW5kZXhPZih2YWwpID49IDBcblx0fSBlbHNlIHtcblx0XHRpZiAodmFsID49IHN0YXJ0VmFsICYmIHZhbCA8PSBlbmRWYWwpIHtcblx0XHRcdHJldHVybiB0cnVlXG5cdFx0fVxuXHR9XG5cblx0cmV0dXJuIGZhbHNlXG59XG5cbi8qKlxuICogQ2hlY2sgdGhlIGRlcGVuZGVuY3kgdmFsdWUgYWdhaW5zdCBhbGwgb2YgaXRzIHF1YWxpZmllcnMuIElmXG4gKiBxdWFsaWZpZXJzIGNvbnRhaW5zIGFuIHVua25vd24gcXVhbGlmaWVyLCB0cmVhdCBpdCBhcyBhIGN1c3RvbVxuICogcXVhbGlmaWVyIGFuZCBleGVjdXRlIHRoZSBmdW5jdGlvbi5cbiAqXG4gKiBAcmV0dXJuIHtCb29sZWFufVxuICovXG5EZXBlbmRlbmN5LnByb3RvdHlwZS5kb2VzUXVhbGlmeSA9IGZ1bmN0aW9uKCkge1xuXHRmb3IgKHZhciBxIGluIHRoaXMucXVhbGlmaWVycykge1xuXHRcdGlmICghdGhpcy5xdWFsaWZpZXJzLmhhc093blByb3BlcnR5KHEpKSBjb250aW51ZVxuXG5cdFx0aWYgKHRoaXMubWV0aG9kcy5pbmRleE9mKHEpICYmIHR5cGVvZiB0aGlzW3FdID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRpZiAocSA9PT0gJ3JhbmdlJykge1xuXHRcdFx0XHRpZiAoIXRoaXNbcV0uYXBwbHkodGhpcywgdGhpcy5xdWFsaWZpZXJzW3FdKSkge1xuXHRcdFx0XHRcdHJldHVybiBmYWxzZVxuXHRcdFx0XHR9XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRpZiAoIXRoaXNbcV0uY2FsbCh0aGlzLCB0aGlzLnF1YWxpZmllcnNbcV0pKSB7XG5cdFx0XHRcdFx0cmV0dXJuIGZhbHNlXG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9IGVsc2UgaWYgKHR5cGVvZiB0aGlzLnF1YWxpZmllcnNbcV0gPT09ICdmdW5jdGlvbicpIHtcblx0XHRcdGlmICghdGhpcy5xdWFsaWZpZXJzW3FdLmNhbGwodGhpcy5xdWFsaWZpZXJzLCB0aGlzLiRlbGUudmFsKCkpKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZVxuXHRcdFx0fVxuXHRcdH1cblx0fVxuXG5cdHJldHVybiB0cnVlXG59XG5cbm1vZHVsZS5leHBvcnRzID0gRGVwZW5kZW5jeVxuXG4vKipcbiAqIEdldCB0aGUgY3VycmVudCBzdGF0ZSBvZiBhIGZpZWxkXG4gKiBAcGFyYW0gIHtqUXVlcnl9ICRlbGUgVGhlIGVsZW1lbnRcbiAqIEByZXR1cm4ge09iamVjdH1cbiAqIEBwcml2YXRlXG4gKi9cbmZ1bmN0aW9uIGdldEZpZWxkU3RhdGUoJGVsZSkge1xuXHR2YXIgdmFsID0gJGVsZS52YWwoKVxuXG5cdC8vIElmIGRlcGVuZGVuY3kgaXMgYSByYWRpbyBncm91cCwgdGhlbiBmaWx0ZXIgYnkgYDpjaGVja2VkYFxuXHRpZiAoJGVsZS5hdHRyKCd0eXBlJykgPT09ICdyYWRpbycpIHtcblx0XHR2YWwgPSAkZWxlLmZpbHRlcignOmNoZWNrZWQnKS52YWwoKVxuXHR9XG5cblx0cmV0dXJuIHtcblx0XHR2YWx1ZTogdmFsLFxuXHRcdGNoZWNrZWQ6ICRlbGUuaXMoJzpjaGVja2VkJyksXG5cdFx0ZGlzYWJsZWQ6ICRlbGUuaXMoJzpkaXNhYmxlZCcpLFxuXHRcdHNlbGVjdGVkOiAkZWxlLmlzKCc6c2VsZWN0ZWQnKVxuXHR9XG59XG5cbi8vIEFycmF5LmlzQXJyYXkgcG9seWZpbGxcbmlmICghQXJyYXkuaXNBcnJheSkge1xuXHRBcnJheS5pc0FycmF5ID0gZnVuY3Rpb24oYXJnKSB7XG5cdFx0cmV0dXJuIE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChhcmcpID09PSAnW29iamVjdCBBcnJheV0nXG5cdH1cbn1cblxuLy8gTnVtYmVyLmlzTmFOIHBvbHlmaWxsXG5OdW1iZXIuaXNOYU4gPSBOdW1iZXIuaXNOYU4gfHwgZnVuY3Rpb24odmFsdWUpIHtcblx0cmV0dXJuIHZhbHVlICE9PSB2YWx1ZVxufVxuXG5cblxuLyoqKioqKioqKioqKioqKioqXG4gKiogV0VCUEFDSyBGT09URVJcbiAqKiAuL3NyYy9kZXBlbmRlbmN5LmpzXG4gKiogbW9kdWxlIGlkID0gNVxuICoqIG1vZHVsZSBjaHVua3MgPSAwXG4gKiovIl0sInNvdXJjZVJvb3QiOiIifQ==