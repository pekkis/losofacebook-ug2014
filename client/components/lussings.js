/**
 * @jsx React.DOM
 */

var React = require('react');

var Post = require('./post.js');

var Lussings = React.createClass({

    getInitialState: function() {
    	return {
    		posts: []
    	};
    },

    componentDidMount: function() {

	    $.get('http://api.losofacebook.tunk.io/api/post/' + this.props.person.id, function(posts) {
	
	        this.setState({
	        	'posts': posts
	        });

	    }.bind(this));
    },

	componentWillReceiveProps: function(props) {

	    $.get('http://api.losofacebook.tunk.io/api/post/' + props.person.id, function(posts) {
	
	        this.setState({
	        	'posts': posts
	        });

	    }.bind(this));
	    
	  },


    urlirizer: function(id, version) {
    	return '/images/' + id + '-' + version + '.jpg';
    },

	render: function() {

	    var postNodes = this.state.posts.map(function(post) { 
	    	return (<Post post={post}></Post>);
	    });

		return (
			
			<div>
				<h3>Wall</h3>

				<ul className="posts">
					{postNodes}
				</ul>
			</div>
		);
	}

});

module.exports = Lussings;