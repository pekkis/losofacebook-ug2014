'use strict';

// Declare app level module which depends on filters, and services
angular
    .module('losofacebook', ['ngSanitize', 'ngCookies', 'losofacebook.filters', 'losofacebook.services', 'losofacebook.directives'])
    .constant('user', 'gaylord.lohiposki')
    .value('currentUser', { 'firstName': 'Gaylord', 'lastName': 'Lohiposki', 'primaryImageId': 469, 'id': 2469079, 'username': 'gaylord.lohiposki' })
    .config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {

    $locationProvider.html5Mode(true);

    $routeProvider
        .when('/', {
            controller: IndexCtrl,
            templateUrl: '/views/index.html'
        })
        .when('/person/:username', {
            controller: PersonCtrl,
            templateUrl: '/views/person.html'
        })
        .when('/company', {
            controller: CompaniesCtrl,
            templateUrl: '/views/companies.html'
        })
        .when('/company/:name', {
            controller: CompanyCtrl,
            templateUrl: '/views/company.html'
        })
        .otherwise({
            redirectTo: '/person/gaylord.lohiposki'
        });

}])
    .run(function($cookies, $browser, user, currentUser, $rootScope) {
        $cookies.user = user;

        //console.debug(currentUser);

        $rootScope.currentUser = currentUser;
    }
);
