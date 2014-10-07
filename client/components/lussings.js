/**
 * @jsx React.DOM
 */

var React = require('react/addons');

var Post = require('./post.js');
var LussingsForm = require('./lussingsform.js');

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

	handleSubmit: function(lussing) {
		
		var newPost = {
			comments: [],
			content: lussing,
			poster_first_name: this.props.person.firstName,
			poster_last_name: this.props.person.lastName,
			poster_primary_image_id: this.props.person.primaryImageId
		};

		var newState = React.addons.update(this.state, {
      		posts : {
        		$unshift : [newPost]
      		}
  		});

		this.setState(newState);
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

				<LussingsForm onLussingSubmit={this.handleSubmit} />

				<ul className="posts">
					{postNodes}
				</ul>
			</div>
		);
	}

});

module.exports = Lussings;