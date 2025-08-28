'use strict';

const e = React.createElement;

class LikeButton extends React.Component {
    constructor(props) {
        super(props);
        if (window.data) {
            window.data.pop();
            this.state = { colors: window.data };
        } else {
            this.state = { colors: [{ from: '#000000', to: '#000000', fuzz: 50 }] };
        }
    }

    changeColorTo(index) {
        return function (e) {
            this.state.colors[index].to = e.target.value;
            this.setState({ colors: this.state.colors });
        }
    }

    changeColorFrom(index) {
        return function (e) {
            this.state.colors[index].from = e.target.value;
            this.setState({ colors: this.state.colors });
        }
    }

    changeRange(index) {
        return function (e) {
            this.state.colors[index].fuzz = e.target.value;
            this.setState({ colors: this.state.colors });
        }
    }

    addPass(e) {
        e.preventDefault();
        this.state.colors.push({ from: '#000000', to: '#000000', fuzz: 50 });
        this.setState({ colors: this.state.colors });
    }

    render() {
        return (
            <div>
                {this.state.colors.map(function (color, index) {
                    return (
                        <div>
                            <span>Pass {index + 1}</span>
                            <div>
                                <label for="head">Color to replace: </label>
                                <input type="color" id="from" name={`from[${index}]`} value={color.from} onChange={this.changeColorFrom(index).bind(this)} />
                            </div>
                            <div>
                                <label for="body">Replace by: </label>
                                <input type="color" id="to" name={`to[${index}]`} value={color.to} onChange={this.changeColorTo(index).bind(this)} />
                            </div>
                            Fuzz: <input type="number" min="0" max="100" value={color.fuzz} onChange={this.changeRange(index).bind(this)} /><input type="range" min="0" max="100" value={color.fuzz} class="slider" id="fuzz" name={`fuzz[${index}]`} onChange={this.changeRange(index).bind(this)} />
                        </div>
                    );
                }, this)}
                <input type="hidden" name="passes" value={this.state.colors.length} />
                <button onClick={this.addPass.bind(this)}>Add 1 pass</button>
            </div>
        );
    }
}

const domContainer = document.querySelector('#coloreditor');
ReactDOM.render(e(LikeButton), domContainer);