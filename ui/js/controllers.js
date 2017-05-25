var grouphealth = angular.module('grouphealth', ['ui.bootstrap','ngMaterial']);

/**
 * API ping allows us to test the server connection
 * this is called on init to ensure proper server configuration
 **/
grouphealth.controller('ping', ['$scope','API', function($scope,API) {
    $scope.ping = function() {
        API.ping()
        .then(function (data) {
            if (data) {
                // api connection established, log the acknolegment
                console.log(data);
            } else {
                // unable to connect to api, likely server missconfiguration issue
                alert("API Connection Failed");
            }
        });
    }
}]);

/**
 * Handle all appointment CRUD opperations
 **/
grouphealth.controller('appointment', ['$scope','$mdDialog','API', function($scope,$mdDialog,API) {
    $scope.reasons = [
        {name:"CheckUp",value:"CheckUp"},
        {name:"Sick",value:"Sick"},
        {name:"Other",value:"Other"},
    ];

    $scope.list = function() {
        alert('test');
    }

    // create opperation uses a custom dialog form, which calls save on submit
    $scope.create = function(ev) {
        $mdDialog.show({
            controller: DialogController,
            templateUrl: 'view/create.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
        })
    };

    $scope.showPrerenderedDialog = function(ev) {
        $mdDialog.show({
            contentElement: '#myDialog',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: false
        });
    };

    function DialogController($scope, $mdDialog, API) {
        $scope.hide = function() {
            $mdDialog.hide();
        };

        $scope.cancel = function() {
            $mdDialog.cancel();
        };

        $scope.save = function(form) {
            console.dir(form);
            API.appointment().add(form)
            .then(function (data) {
                if (data) {
                    console.log(data);
                    alert('saved');
                    $mdDialog.hide(form);
                } else {
                    alert("Failed to save appointment");
                }
            });
        };
    }


    $scope.remove = function(id) {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
            .textContent('This cannot be undone. Are you sure?')
            .ok('Remove')
            .cancel('Cancel');

        $mdDialog.show(confirm).then(function() {
            // call API destroy method
            API.appointment().remove(id)
            .then(function (data) {
                if (data) {
                    console.log(data);
                    alert('Removed '+id);
                } else {
                    alert("Failed to remove appointment "+id);
                }
            });
        }, function() {
            // we don't actually have to do anything on cancel (the dialog closes itself
            // but not having this callback causes a warning in debuggers
        });
    };
}]);


/**
 * Basic login controller
 ** /
grouphealth.controller('login', ['$scope','API', function($scope,API) {
    $scope.logout = function () {
        sessionStorage.access_token = null;
        sessionStorage.username = null;
        $state.go('login');
    };

    $scope.login = function (username, password) {
        API.login(username, password)
        .then(function (data) {
            if (data) {
                // set session token and go to dashboard
                sessionStorage.access_token = data.access_token;
                sessionStorage.username = data.username;
                $state.go('home');
            } else {
                // this would be better set somewhere else, but since login
                // isn't even part of the spec lets just make this super simple :)
                alert('Invalid username/password');
            }
        });
    };
}
/**/


/*
var app = angular.module('form-example1', []);

var INTEGER_REGEXP = /^-?\d+$/;
app.directive('integer', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.integer = function(modelValue, viewValue) {
        if (ctrl.$isEmpty(modelValue)) {
          // consider empty models to be valid
          return true;
        }

        if (INTEGER_REGEXP.test(viewValue)) {
          // it is valid
          return true;
        }

        // it is invalid
        return false;
      };
    }
  };
});

app.directive('username', function($q, $timeout) {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      var usernames = ['Jim', 'John', 'Jill', 'Jackie'];

      ctrl.$asyncValidators.username = function(modelValue, viewValue) {

        if (ctrl.$isEmpty(modelValue)) {
          // consider empty model valid
          return $q.resolve();
        }

        var def = $q.defer();

        $timeout(function() {
          // Mock a delayed response
          if (usernames.indexOf(modelValue) === -1) {
            // The username is available
            def.resolve();
          } else {
            def.reject();
          }

        }, 2000);

        return def.promise;
      };
    }
  };
});
*/
