## Elasticsearch

#### Available Commands 

   `php artisan elastic:clear`  Elasticsearch data clear  
   `php artisan elastic:import` Elasticsearch data import  
   `php artisan elastic:update` Elasticsearch data update  

#### Add

To add a model to elastic search, need:

1. Add in `config/elasticsearch.php` to `searchable_config`

    ```
    Model::class => [
        Config::FIELDS => [
            'name',
            'phone'
        ],
        Config::PARAM_ROUTE => 'model',
        Config::PARAM_PERMISSION_ROUTE => 'model.update',
        Config::RELATIONS => [
            Relation::class => Config::RELATIONS_ARRAY[Relation::class]
        ]
    ]
    ```
    where:
    
    `Config::FIELDS` (required) data from the model need to add.
    
    `Config::PARAM_ROUTE` (required) route to this model page.
    
    `Config::PARAM_PERMISSION_ROUTE` (required) route to implement user permission 
    
    `Config::RELATIONS` relations of this model for search by.
        
     To implement searching by related table need to add in `App/Libraries/Elasticsearch/Config.php` to `RELATIONS_ARRAY`
    
    ```
    Relation::class => [
         'name' => 'relation_name',
         'fields' => [
             'field1',
             'field2',
              ...
         ],
         'dependent_relationships' => [
             'relation1',
             'relation2',
              ...
         ]
     ]
    ```
    where:
    
    `name` (required) name of relation method 
    
    `fields` (required) fields which will be added for searching
    
    `dependent_relationships` (required) relation which will be reinitialized in case of changes in `Relation::class` entities (before adding new relation to array, pay attention if it exists in `Relation::class`)
    
2. In `routes_api.php` need create route 

    ```
    Route::get('/{model}', 'ModelController@get')->name('model.get');
    ```
 
    and add to `config/laratrust_seeder.php` for all roles

3. In controller create get method

    ```
    public function get(User $user)
    { 
        return response()->json($user);
    }
    ```

4. In Vue endpoints `resources/assets/js/includes/Admin/endpoints.js` add

    ```
    export const getModel = params => axios.get(zRoute('model.get', { model: params.id }));
    ```

5. In Vue component need add

    ```
    import {getModel} from '../../../includes/endpoints';
    ```

    and add method
    
    ```
    mounted() {
        this.listenSearchEvent(getModel, 'ComponentRouteName');
    }
    ```