<p align="center">
    <img  height="80" src="https://cdn.rawgit.com/ElemeFE/element/dev/element_logo.svg">
    <img width="88" height="80" src="https://vuejs.org/images/logo.png">
    <img width="300" src="https://laravel.com/assets/img/components/logo-laravel.svg">
</p>

## Laravel + Vue + ElementUI 2.x + Vue Admin

- Based on [Vue-Admin](https://github.com/taylorchen709/vue-admin) using [ElementUI](http://element.eleme.io/).
- Login using [JWT](https://jwt.io/) with [Vue-Auth](https://github.com/websanova/vue-auth), [Axios](https://github.com/mzabriskie/axios) and [JWT-Auth](https://github.com/tymondesigns/jwt-auth).
- JS routing with [Ziggy](https://tightenco.github.io/ziggy/0.6/).
- ACL with [Vue-Auth](https://github.com/websanova/vue-auth).
- Modular system implemented. Config in **config/modular.php**
- The api routes, are separate for each module, in **Modules/{ModuleName}/routes_api.php**, so Vue make ajax request using this routes
- [FontAwesome](http://fontawesome.io/icons/) icons

## Install

- `composer install`
- `yarn install`
- `cp .env.example .env` and add your DB credentials and app URL
- `php artisan jwt:secret -f`
- `php artisan key:generate`
- `php artisan migrate --seed`
- `php artisan storage:link` to create storage symlink in public folder (delete **public/storage** if has error, and re-run)
- `php artisan ziggy:generate "resources/assets/js/includes/ziggy.js"` to generate routes file
- `php artisan serve` and open browser
- Login with `super-admin@app.com`. Password `password`

##For Developers

- `php artisan ide-helper:models --reset --write` updates models meta
- `php artisan permissions:update` updates role permissions (**config/laratrust_seeder.php** to config)
- `php artisan permissions:clear` delete unused permissions (**config/laratrust_seeder.php** to config)
- `php artisan make:module {Role}\{ModulName} --all` create module (Model, Controller, Routes, Migration, VueComponent)
- `yarn watch` run watcher to compile JS in real time

### PHPStorm IDE settings
Path: **File | Settings | Languages & Frameworks | JavaScript | Webpack**

Make sure that webpack configuration file path is presented and correct.

#### Code style
##### Javascript
Path: **File | Settings | Editor | Code Style | JavaScript**

Click on **Select from...** link and select **Language** > **TypeScript**.

Make sure that you have these options:

On tab **Tabs and Indents**:
- Use tab character: **false**
- Smart tabs: **false**
- Tab size: **4**
- Indent: **4**
- Continuation Indent: **4**
- Keep indents on empty lines: **false**
- Indent chained methods: **true**
- Indent all chained calls in a group: **true**

On tab **Wrapping and Braces**.
- **Hard wrap at** = 180

##### PHP
Path: **File | Settings | Editor | Code Style | PHP**

Click on **Select from...** link and select **Predefined Style** > **PSR1/PSR2**.

Make sure that you have these options:

On tab **Tabs and Indents**:
- Use tab character: **false**
- Smart tabs: **false**
- Tab size: **4**
- Indent: **4**
- Continuation Indent: **4**
- Keep indents on empty lines: **false**
- Indent code in PHP tags: **false**

On tab **Wrapping and Braces**.
- **Hard wrap at** = 200

### ESLint options
Options are defined in `.eslintrc.js` file.
If your IDE doesn't see that settings file, you should point on it manually.

For example, for PHPStorm go to the next path: **File | Settings | Languages & Frameworks | JavaScript | Code Quality Tools | ESLint**

- Set **Manual ESLint configuration** to **true**.
- Set your **NodeJS interpreter** path (by default on WindowsOS: `C:\Program Files\nodejs\node.exe`).
- Set your **ESLint package** path (by default on WindowsOS: `C:\your_project\node_modules\eslint`).
- Set `.eslintrc.js` configuration file path. This point is optional.

To fix ESLint problems and format whole file, select `Fix ESLint problems` in file context menu.

To reformat all `.js` and `.vue` files in project by ESLint rules, use next command:
`npm run lint`.

### git branches
Main CRM is called **Momentum**. It can be found in `master` branch.

There're other several projects (in branches) based on the Momentum CRM, e.g.: niagara, resume4mat, springboard etc.

Branches should be checkouted from `production-project-name`, for example, `production-niagara`.

Branch naming template: `{project-name|core}/{feature|bugfix|ect}/{task number}-{kebab-case-task-name}`.

Example: `niagara/feature/SD-73-core-module-updates`.

### git commits
All developers should follow smart commit [rules](https://confluence.atlassian.com/fisheye/using-smart-commits-960155400.html).

Common commit message example:
```
SD-90 Auth Page fixing
SD-90 Login Page fixing: added new validation rule
SD-90 ...
```
All committed changes should be reflected in commit messages.

### Screencasting
Useful Chrome extension for screencast: https://www.screencastify.com.
After task is complete and deployed, developer has to test it manually on staging/production,
and add a link with video file in the task.

#### JS-variables

You have to do the following if you want to export some variables to JS (frontend):

- Define constant or PUBLIC STATIC (!) variable in Model class with value type as integer, string or array.
- If you wan't to hide some vars/constants from model class just put them into `$exceptions` array to config file `/config/global_with_js.php`
- If you want to add custom variable (not as class member) put them into `CUSTOM VARIABLES` section
- You may add a custom class to `$customClasses` array. Constants/variable from these classes will be appended to output array

Look at this example in `app/Models/Color/Color`

```
class Color extends Model {
    const COLOR_RED = 0;
    const COLOR_BLACK = 1;
    
    public static $colors = [
        self::COLOR_RED => 'Red',
        self::COLOR_BLACK => 'Black'
    ];
}
``` 

To use exported variables directly in component template yo need to:

- import util as `import {computed} from 'path/includes/_common_/util';`
- extend `computed` property as `computed: { ...computed }`
- call your variable in <template> section as `{{ Laravel.Color.COLOR_BLACK }}` or `{{ Laravel.Color.colors }}`

The entire list of all exported variables you can see in page source code (`window.Laravel` variable)

## [Elasticsearch...](./docs/elastic.md)

## Export/Import

1. Setup model

    ```
    public $duplicateCheck = ['display_name'];

    public $importFields = ['display_name', 'trading_name', 'abn', ...];

    public $exportFields = ['id', 'display_name', 'trading_name', ...];

    // uses $exportFields if left empty
    public $exportHeadings = [];
    ```

2. Include ImportExport trait in controller

    ```
    use App\Traits\ImportExport;
    ```
3. Include trait in class

    ```
    class ModelController extends Controller
    {
        use ImportExport;  
    ```  
4. Add vue component to page
    ```
    <import-export :exportUrl="" :importUrl="" v-on:importSuccess=""></import-export>
    ```
   where:

   `:exportUrl` & `:importUrl` imported zRoute endpoint from `~/includes/endpoints`

   `v-on:importSuccess` call function to refresh data in view

#### Export
1. Create export method

    ```
    public function export(Request $request)
    {
        $query = $this->buildQuery($request);
        return $this->entityExport($query);
    }
    ```
   where:

   `$query` model query

2. In Vue endpoints `resources/assets/js/includes/[role]/endpoints.js` add

    ```
    export const exportModel = params => axios.get(zRoute('model.export', params), { responseType: 'blob' });
    ```
3. In Vue component import endpoint

    ```
    import {exportModel} from '~/includes/endpoints';
    ```
4. In Vue component add method
    ```
    handleExport() {
        let params = {
            search: this.filters.search,
            sortBy: this.sortBy
        };        
        let fileName = 'export_models_' + moment().format('YYYY_MM_DD');
        
        exportModel(params).then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', fileName + '.xlsx');
            document.body.appendChild(link);
            link.click();
        });
    }
    ```
   where:

   `params` used by query in controller

   `fileName` name of downloaded file

#### Import

1. Create import method

    ```
    public function import(Request $request)
    {
        $this->entityImport($request, Model::class);
    }
    ```

2. In Vue endpoints `resources/assets/js/includes/[role]/endpoints.js` add

    ```
    export const importModel = params => axios.post(zRoute('model.import'), params);
    ```
3. In Vue component import endpoint

    ```
    import {importModel} from '~/includes/endpoints';
    ```
4. In Vue component add method
    ```
    handleImport(event) {
        const data = new FormData();
        data.append('file', event.file);
        importModel(data).then((response) => {
            this.$refs.upload.clearFiles();
            this.$message.success('Successfully imported');
        }).catch(e => {
            this.$errors.handle(e);
        });
    },
    ``` 
5. Add upload field
    ``` 
    <el-upload
        drag
        ref="upload"
        :action="zRoute('model.import')"
        :http-request="handleImport"
        :auto-upload="false"
        accept=".csv, .xls, xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
        >
            <i class="el-icon-upload"></i>
            <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
            <div class="el-upload__tip" slot="tip">Supported formats: .xlsx / .xls / .csv</div>
            <div class="el-upload__tip" slot="tip">Valid columns: [name, email, phone]</div>
    </el-upload>   
    ```     
6. Trigger import
    ``` 
    this.$refs.upload.submit();  
    ```   

## Xero

#### Fetch
- In controller
    -   `use XeroPHP\Application\PrivateApplication;`
    - In function
        - `$xeroConfig = config('xero.config');`
        - `$xero = new PrivateApplication($xeroConfig);`
        - All: `$query = $xero->load('PayrollAU\\Timesheet');`
        - Single: `$query = $xero->loadByGUID('Accounting\\Contact', $GUID);`

#### Create
- In controller
    - `use XeroPHP\Application\PrivateApplication;`
    - `use XeroPHP\Models\Accounting\Invoice\LineItem;`
    - In function
        - `$xeroConfig = config('xero.config');`
        - `$xero = new PrivateApplication($xeroConfig);`
        - `$xeroInvoice = new Invoice($xero);`
        - Set data
        - `$xeroInvoice->setType($request['Type']);`
        - `$xeroInvoice->setContact($invoiceContact);`
            - etc etc...
        - `$xeroInvoice->save();`

## Error handling

for more info [resources/assets/js/plugins/Errors/readme.md](./resources/assets/js/plugins/Errors/readme.md)

For error handling used global property `this.$errors` available in each components.
It automatically creates Errors class `/resources/assets/js/plugins/Errors/Errors.js`

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

**Examples**:

Simple usage
```js
export default {
    methods: {
        submit() {
            storeUser(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => this.formLoading = false);
        }
    }
}
```


Handle error and call some additional actions
```js
export default {
    methods: {
        submit() {
            storeUser(this.form)
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


Handle error and call additional actions when exists error for some field
```js
export default {
    methods: {
        submit() {
            storeUser(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => {
                    this.formLoading = false;

                    if (this.$errors.has('phone')) {
                        this.loadPhones();
                    }
                });
        }
    }
}
```

Listen errors for some important fields and take the necessary actions
```js
export default {
    methods: {
        submit() {
            storeEmail(this.form)
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

Print first error
```js
export default {
    methods: {
        submit() {
            storeAppointment(this.form)
                .then(({data}) => this.$message.success(data.message))

                .catch(this.$errors.handle)

                .finally(() => {
                    this.formLoading = false;

                    this.$errors.printFirstError();
                })
        }
    }
}
```
