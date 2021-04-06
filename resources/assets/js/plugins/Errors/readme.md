# Errors handler

## Table of Contents

- [Note](#note)
- [Methods](#methods)
  - [all](#all)
  - [has](#has)
  - [any](#any)
  - [get](#get)
  - [set](#set)
  - [record](#record)
  - [clear](#clear)
  - [handle](#handle)
  - [onHasError](#onHasError)
  - [onHasAnyError](#onHasAnyError)
  - [printFirstError](#printFirstError)
- [Handle examples](#handle-examples)
    - [Simple short usage](#simple-usage-with-short-version)
    - [Simple usage with another actions](#simple-usage-with-another-actions)
- [Refactoring examples](#refactoring-examples)
    - [Refactoring example 1](#refactoring-example-1)
    - [Refactoring example 2 with error listeners](#refactoring-example-2-with-error-listeners)
    
## Note

The `Errors` class is automatically created for each component where `this.$errors` is called. 
It is installed as a global `Vue` plugin and does not need to be imported into each component.

**IMPORTANT: Inside the Error class used a vue instance. If you want to change the error state and immediately access it for new data, you need to wait until the vue instance is updated:**
```js
export default {
    methods: {
        submit() {
            storeContact(this.form)
                .catch(e => {
                    this.$errors.handle(e);

                    this.$errors.get('email'); // null
                })

                .catch(e => {
                    this.$errors.handle(e).then(() => {
                        this.$errors.get('email'); // 'Email error'
                    });
                })

                .catch(async e => {
                    await this.$errors.handle(e);

                    this.$errors.get('email'); // 'Email error'
                })

                .catch(async e => {
                    this.$errors.handle(e);

                    await this.$errors.$nextTick();

                    this.$errors.get('email'); // 'Email error'
                })
        }
    }
}
```

## Methods

### .all()
Get object with all errors in component
```js
this.$errors.all();
/**
 * {
 *     email: 'Email is required',
 *     phone: [
 *         'Phone is required'
 *     ]
 * }
 */
```

### .has()
Determine if any errors exists for the given field
```js
this.$errors.has('name'); // false
this.$errors.has('email'); // true
```


### .any()
Determine if we have any errors
```js
this.$errors.any(); // true
```

### .get()
Retrieve the error message for a field

```js
this.$errors.get('email'); // 'Email is required'

this.$errors.get('name'); // null

// With default value
this.$errors.get('name', 'Name too short'); // 'Name too short'
```

### .set()
Set the error message for a field
```js
this.$errors.get('name'); // null

this.$errors.set('name', 'Name is required');

this.$errors.get('name'); // 'Name is required'
```

### .record()
Record the new errors
```js
this.$errors.all(); // {}

this.$errors.record({
    email: 'Email is required',
    password: ['Password is required']
});

this.$errors.all();
/**
* {
*     email: 'Email is required',
*     password: [
*         'Password is required'
*     ]
* }
*/
```

### .clear()
Clear a specific field or all errors
```js
this.$errors.all();
/**
* {
*     email: 'Email is required',
*     password: [
*         'Password is required'
*     ]
* }
*/

this.$errors.clear();

this.$errors.all(); // {}

this.$errors.clear('password');

this.$errors.all();
/**
* {
*     email: 'Email is required'
* }
*/
```
### .handle()
Handle error
```js
action(this.form)
    .then(res => {
        this.$success()
    })
    .catch(this.$errors.handle);

// Or
action(this.form)
    .then(res => {
        this.$success() 
    })
    .catch(e => {
        this.$errors.handle(e);

        this.someAdditionalAction();
    });
```

### .onHasError()
Call callback when determined error for field
```js
this.$errors.onHasError('email', emailError => {
    
});
```

### .onHasAnyError()
Call callback when determined error for any of fields
```js
this.$errors.onHasAnyError('email|password', (field, error) => {
    
});

// Or using array of field names
this.$errors.onHasAnyError(['name', 'phone'], (field, error) => {
    
});
```

### .printFirstError()
Print first of exists error
```js
this.$errors.printFirstError();

// Or you can pass your function to display error
this.$errors.printFirstError(
    this.$message.success
);

this.$errors.printFirstError(
    alert
);

this.$errors.printFirstError(
    console.error
);

this.$errors.printFirstError(e => {
    // some custom print
});
```

## Handle examples

### Simple usage with short version
```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => this.formLoading = false);
        }
    }
}
```

### Simple usage with another actions
```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(e => {
                    this.$errors.handle(e);
            
                    this.someAdditionalAction();
                })

                .finally(() => this.formLoading = false);
        }
    }
}
```

## Refactoring examples

### Refactoring example 1

#### Before
```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(response => {
                    this.formLoading = false;

                    this.$message.success(response.data.message);
                })
                .catch((error) => {
                    if (Laravel.appDebug) {
                        console.log(error);
                    }
                    if (error.response.data.errors) {
                        if (error.response.data.errors.appointment_availability) {
                            this.$refs.AppointmentConsEdit.handleAppointments();
                        } else {
                            this.errors.record(error.response.data.errors);
                        }

                    } else if (error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Unknown server error');
                    }
                    this.formLoading = false;
                });
        }
    }
}
```

#### After
```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => {
                    this.formLoading = false;

                    if (this.$errors.has('appointment_availability')) {
                        this.$refs.AppointmentConsEdit.handleAppointments();
                    }
                });
        }
    }
}
```

### Refactoring example 2 with error listeners

#### Before
```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(response => {
                    this.formLoading = false;
                    
                    this.$message.success(response.data.message);
                })
                .catch(error => {
                    let catchError = 0;

                    if (Laravel.appDebug) {
                        console.log(error);
                    }
                    if (error.response.data.errors) {
                        this.errors.record(error.response.data.errors);

                        Object.keys(error.response.data.errors).find(
                            function (element) {
                                switch (element) {
                                    case 'to_ids.users':
                                    case 'to_ids.contacts':
                                    case 'to_ids.groups':
                                        catchError++;
                                        break;
                                }
                            }
                        );

                        if (catchError > 0) {
                            this.$message.error('At least 1 recipient required');
                            this.activeTab = 'users';
                        }

                    } else if (error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Unknown server error');
                    }
                    this.formLoading = false;
                });
        }
    }
}
```

#### After

```js
export default {
    methods: {
        submit() {
            action(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => this.formLoading = false);
        }
    },
    mounted() {
        this.$errors.onHasAnyError(['to_ids.users', 'to_ids.contacts', 'to_ids.groups'], () => {
            this.$message.error('At least 1 recipient required');
            
            this.activeTab = 'users';
        });
    }
}
```