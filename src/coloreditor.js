'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var e = React.createElement;

var LikeButton = function (_React$Component) {
    _inherits(LikeButton, _React$Component);

    function LikeButton(props) {
        _classCallCheck(this, LikeButton);

        var _this = _possibleConstructorReturn(this, (LikeButton.__proto__ || Object.getPrototypeOf(LikeButton)).call(this, props));

        if (window.data) {
            window.data.pop();
            _this.state = { colors: window.data };
        } else {
            _this.state = { colors: [{ from: '#000000', to: '#000000', fuzz: 50 }] };
        }
        return _this;
    }

    _createClass(LikeButton, [{
        key: 'changeColorTo',
        value: function changeColorTo(index) {
            return function (e) {
                this.state.colors[index].to = e.target.value;
                this.setState({ colors: this.state.colors });
            };
        }
    }, {
        key: 'changeColorFrom',
        value: function changeColorFrom(index) {
            return function (e) {
                this.state.colors[index].from = e.target.value;
                this.setState({ colors: this.state.colors });
            };
        }
    }, {
        key: 'changeRange',
        value: function changeRange(index) {
            return function (e) {
                this.state.colors[index].fuzz = e.target.value;
                this.setState({ colors: this.state.colors });
            };
        }
    }, {
        key: 'addPass',
        value: function addPass(e) {
            e.preventDefault();
            this.state.colors.push({ from: '#000000', to: '#000000', fuzz: 50 });
            this.setState({ colors: this.state.colors });
        }
    }, {
        key: 'render',
        value: function render() {
            return React.createElement(
                'div',
                null,
                this.state.colors.map(function (color, index) {
                    return React.createElement(
                        'div',
                        null,
                        React.createElement(
                            'span',
                            null,
                            'Pass ',
                            index + 1
                        ),
                        React.createElement(
                            'div',
                            null,
                            React.createElement(
                                'label',
                                { 'for': 'head' },
                                'Color to replace: '
                            ),
                            React.createElement('input', { type: 'color', id: 'from', name: 'from[' + index + ']', value: color.from, onChange: this.changeColorFrom(index).bind(this) })
                        ),
                        React.createElement(
                            'div',
                            null,
                            React.createElement(
                                'label',
                                { 'for': 'body' },
                                'Replace by: '
                            ),
                            React.createElement('input', { type: 'color', id: 'to', name: 'to[' + index + ']', value: color.to, onChange: this.changeColorTo(index).bind(this) })
                        ),
                        'Fuzz: ',
                        React.createElement('input', { type: 'number', min: '0', max: '100', value: color.fuzz, onChange: this.changeRange(index).bind(this) }),
                        React.createElement('input', { type: 'range', min: '0', max: '100', value: color.fuzz, 'class': 'slider', id: 'fuzz', name: 'fuzz[' + index + ']', onChange: this.changeRange(index).bind(this) })
                    );
                }, this),
                React.createElement('input', { type: 'hidden', name: 'passes', value: this.state.colors.length }),
                React.createElement(
                    'button',
                    { onClick: this.addPass.bind(this) },
                    'Add 1 pass'
                )
            );
        }
    }]);

    return LikeButton;
}(React.Component);

var domContainer = document.querySelector('#coloreditor');
ReactDOM.render(e(LikeButton), domContainer);