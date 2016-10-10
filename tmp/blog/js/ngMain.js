/**
 * Created by cdd on 2016/7/12.
 */
var myApp = angular.module('myApp',['ngRoute']);

myApp.config(['$routeProvider',function ($routeProvider) {
    $routeProvider
        .when('/web-front-end',{
            templateUrl: 'apages/web-front-end.html'
        })
        .when('/HTML5',{
            templateUrl: 'apages/HTML5.html',
            controller: 'H5'
        })
        .when('/CSS3',{
            templateUrl: 'apages/CSS3.html',
            controller: 'C3'
        })
        .when('/JavaScript',{
            templateUrl: 'apages/JavaScript.html',
            controller: 'Jt'
        })
        .when('/Frames',{
            templateUrl: 'apages/Frames.html',
            controller: 'Fs'
        })
        .when('/About_Me',{
            templateUrl: 'apages/About_Me.html',
            controller: 'Ae'
        })
        .when('/Notes',{
            templateUrl: 'apages/Notes.html',
            controller: 'Ns'
        })
        .otherwise({
            redirectTo: 'apages/web-front-end'
        })
}]);

myApp.controller('Main', ['$scope','$location', function ($scope, $location) {
    $scope.toFrames = function() {
        $location.path('/Frames');
    };
}]);
myApp.controller('H5',['$scope','$location',function($scope, $location) {

}]);
myApp.controller('C3',['$scope','$location',function($scope, $location) {

}]);
myApp.controller('Jt',['$scope','$location',function($scope, $location) {

}]);
myApp.controller('Fs',['$scope','$location',function($scope, $location) {

}]);
myApp.controller('Ae',['$scope','$location',function($scope, $location) {

}]);
myApp.controller('Ns',['$scope','$location',function($scope, $location) {

}]);