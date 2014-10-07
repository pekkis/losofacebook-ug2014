/**
 * @jsx React.DOM
 */

var React = require('react');

var WallHeader = require('./wallheader.js');

var WallHeader = React.createClass({

    backgroundImage: function() {
    	return 'https://place.manatee.lc/' + this.props.person.backgroundId + '/1140/250.jpg';
    },

    urlirizer: function(version) {
    	return '/images/' + this.props.person.primaryImageId + '-' + version + '.jpg';
    },

	render: function() {
				
	    console.log(this.props);

		return (
			<div className="row">
			    <div className="col-md-12">
			        <img src={ this.backgroundImage() } />

			        <div className="row">
			            <div className="col-md-3">
			                <img src={this.urlirizer('main')} id="profile-image" />
			            </div>

			            <div className="col-md-9">
			                <h1>{ this.props.person.firstName } { this.props.person.lastName } ({ this.props.person.id })</h1>
	
		                    <div id="basic-info" className="col-md-10">
		                        <div>
		                            { this.props.person.occupation } at <strong>{ this.props.person.company }</strong>
		                        </div>
		                        <div>
		                            Birthday: { this.props.person.birthday}
		                        </div>

		                    </div>
			            </div>
			        </div>
			    </div>
			</div>
		);
	}

});

module.exports = WallHeader;