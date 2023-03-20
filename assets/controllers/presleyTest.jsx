import React, { useRef, useState } from 'react'
import ReactDOM from 'react-dom'
import axios from "axios";

const mode = 'login';

class LoginComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            mode: this.props.mode,
            fullname: '',
            email: '',
            password: '',
            registration_form_agreeTerms: '',
            fullnameError: '',
            emailError: '',
            passwordError: '',
            successMessage: '',
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    // because our form inputs' names are the same
    // as our state names, we can just use them as the state names
    handleChange(e) {
        console.log(e.target);
        var val = e.target.name;
        // if (e.target.name === 'fullname'){
        //     this.setState({
        //         fullname: e.target.value
        //     });
        // }else
        if (e.target.name === 'registration_form[email]' || e.target.name === '_username'){
            this.setState({
                email: e.target.value
            });
        }else if(e.target.name === 'registration_form[plainPassword]' || e.target.name === '_password') {
            this.setState({
                password: e.target.value
            });
        }else {
            this.setState({
                registration_form_agreeTerms: e.target.registration_form_agreeTerms
            });
        }

    }
    toggleMode() {
        var newMode = this.state.mode === 'login' ? 'signup' : 'login';
        this.setState({ mode: newMode});
    }
    handleSubmit(e) {
        e.preventDefault();

        console.log('ici');

        // var  fullname =  this.state.fullname;
        let url = undefined;
        const urlRegister = document.getElementById('test').getAttribute('data-url-register');
        const urlLogin = document.getElementById('test').getAttribute('data-url-login');
        const bodyFormData = new FormData();
        if (this.state.mode === 'signup'){
             url = urlRegister;
            bodyFormData.append('registration_form[email]',document.getElementById('email').value);
            console.log(document.getElementById('email').value);
            console.log(document.getElementById('createpassword').value);
            bodyFormData.append('registration_form[plainPassword]',document.getElementById('createpassword').value);
        }else{
             url = urlLogin;
            bodyFormData.append('_username',document.getElementById('username').value);
            bodyFormData.append('_password',document.getElementById('password').value);
        }
        console.log(bodyFormData.values());

        var registration_form_agreeTerms =  this.state.registration_form_agreeTerms;

        console.log('//////////////////////');
        console.log(url);
        axios.post(url, bodyFormData)
            .then(function (response) {
                console.log(response);
                window.location.href = response.data.urlRedirection ? response.data.urlRedirection : '/';
            })
            .catch(function (error) {
                console.log(error);
            });
    }
    render() {
        return (
            <div>
                <div className={`form-block-wrapper form-block-wrapper--is-${this.state.mode}`} ></div>
                <section className={`form-block form-block--is-${this.state.mode}`}>
                    <header className="form-block__header">
                        <h1>{this.state.mode === 'login' ? 'Welcome back!' : 'Sign up'}</h1>
                        <div className="form-block__toggle-block">
                            <span>{this.state.mode === 'login' ? 'Don\'t' : 'Already'} have an account? Click here &#8594;</span>
                            <input id="form-toggler" type="checkbox" onClick={this.toggleMode.bind(this)} />
                            <label htmlFor="form-toggler"></label>
                        </div>
                    </header>
                    <LoginForm mode={this.state.mode} onSubmit={this.handleSubmit} />
                </section>
            </div>
        )
    }
}

class LoginForm extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <form onSubmit={this.props.onSubmit}>
                <div className="form-block__input-wrapper">
                    <div className="form-group form-group--login">
                        <Input type="text" id="username" name="_username" onChange={this.handleChange} disabled={this.props.mode === 'signup'}/>
                        <Input type="password" id="password" name="_password" onChange={this.handleChange} disabled={this.props.mode === 'signup'}/>
                    </div>
                    <div className="form-group form-group--signup">
                        <Input type="text" id="fullname" label="full name" disabled={this.props.mode === 'login'} />
                        <Input type="email" id="email" label="email" name="registration_form[email]" disabled={this.props.mode === 'login'} />
                        <Input type="password" id="createpassword" label="password" name="registration_form[plainPassword]" disabled={this.props.mode === 'login'} />
                        <Input type="password" id="repeatpassword" label="repeat password" disabled={this.props.mode === 'login'} />
                    </div>
                </div>
                <button className="button button--primary full-width" type="submit">{this.props.mode === 'login' ? 'Log In' : 'Sign Up'}</button>
            </form>
        )
    }
}

const Input = ({ id, type, label, disabled }) => (
    <input className="form-group__input" type={type} id={id} placeholder={label} disabled={disabled}/>
);

const App = () => (

    <div className={`app app--is-${mode}`}>
        <LoginComponent
            mode={mode}
            onSubmit={
                function() {
                    console.log('submit');
                    console.log({mode});
                }
            }
        />
    </div>
);

ReactDOM.render( <App/>, document.getElementById("test"));



