<?php

// This file is auto-generated and is for apps only. Bundles SHOULD NOT rely on its content.

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Config\Loader\ParamConfigurator as Param;

/**
 * This class provides array-shapes for configuring the services and bundles of an application.
 *
 * Services declared with the config() method below are autowired and autoconfigured by default.
 *
 * This is for apps only. Bundles SHOULD NOT use it.
 *
 * Example:
 *
 *     ```php
 *     // config/services.php
 *     namespace Symfony\Component\DependencyInjection\Loader\Configurator;
 *
 *     return App::config([
 *         'services' => [
 *             'App\\' => [
 *                 'resource' => '../src/',
 *             ],
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type ImportsConfig = list<string|array{
 *     resource: string,
 *     type?: string|null,
 *     ignore_errors?: bool,
 * }>
 * @psalm-type ParametersConfig = array<string, scalar|\UnitEnum|array<scalar|\UnitEnum|array<mixed>|Param|null>|Param|null>
 * @psalm-type ArgumentsType = list<mixed>|array<string, mixed>
 * @psalm-type CallType = array<string, ArgumentsType>|array{0:string, 1?:ArgumentsType, 2?:bool}|array{method:string, arguments?:ArgumentsType, returns_clone?:bool}
 * @psalm-type TagsType = list<string|array<string, array<string, mixed>>> // arrays inside the list must have only one element, with the tag name as the key
 * @psalm-type CallbackType = string|array{0:string|ReferenceConfigurator,1:string}|\Closure|ReferenceConfigurator|ExpressionConfigurator
 * @psalm-type DeprecationType = array{package: string, version: string, message?: string}
 * @psalm-type DefaultsType = array{
 *     public?: bool,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 * }
 * @psalm-type InstanceofType = array{
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type DefinitionType = array{
 *     class?: string,
 *     file?: string,
 *     parent?: string,
 *     shared?: bool,
 *     synthetic?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     configurator?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     decorates?: string,
 *     decoration_inner_name?: string,
 *     decoration_priority?: int,
 *     decoration_on_invalid?: 'exception'|'ignore'|null,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 *     from_callable?: CallbackType,
 * }
 * @psalm-type AliasType = string|array{
 *     alias: string,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 * }
 * @psalm-type PrototypeType = array{
 *     resource: string,
 *     namespace?: string,
 *     exclude?: string|list<string>,
 *     parent?: string,
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type StackType = array{
 *     stack: list<DefinitionType|AliasType|PrototypeType|array<class-string, ArgumentsType|null>>,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 * }
 * @psalm-type ServicesConfig = array{
 *     _defaults?: DefaultsType,
 *     _instanceof?: InstanceofType,
 *     ...<string, DefinitionType|AliasType|PrototypeType|StackType|ArgumentsType|null>
 * }
 * @psalm-type ExtensionType = array<string, mixed>
 * @psalm-type PimcoreStaticRoutesConfig = array{
 *     definitions?: list<array{ // Default: []
 *         name?: scalar|Param|null,
 *         pattern?: scalar|Param|null,
 *         reverse?: scalar|Param|null,
 *         controller?: scalar|Param|null,
 *         variables?: scalar|Param|null,
 *         defaults?: scalar|Param|null,
 *         siteId?: list<int|Param>,
 *         methods?: list<scalar|Param|null>,
 *         priority?: int|Param,
 *         creationDate?: int|Param,
 *         modificationDate?: int|Param,
 *     }>,
 *     config_location?: array{
 *         staticroutes?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *     },
 * }
 * @psalm-type PimcoreOpenSearchClientConfig = array{
 *     clients?: array<string, array{ // Default: []
 *         name?: scalar|Param|null,
 *         hosts?: list<scalar|Param|null>,
 *         logger_channel?: scalar|Param|null, // Logger channel to be used for opensearch client logs // Default: "pimcore.opensearch.default"
 *         log_404_errors?: bool|Param, // Enables logging of 404 errors (default: false) // Default: false
 *         username?: scalar|Param|null, // Username for opensearch authentication // Default: "admin"
 *         password?: scalar|Param|null, // Password for opensearch authentication // Default: "admin"
 *         ssl_key?: scalar|Param|null, // Path to private SSL key file (.key)
 *         ssl_cert?: scalar|Param|null, // Path to PEM formatted SSL cert file (.cert)
 *         ssl_password?: scalar|Param|null, // If private key and certificate require a password (default: null)
 *         ssl_verification?: bool|Param, // Enable or disable the SSL verification (default: true)
 *         aws_region?: scalar|Param|null, // Will set the setSigV4Region()
 *         aws_service?: scalar|Param|null, // Will set the setSigV4ServicesetSigV4Service()
 *         aws_key?: scalar|Param|null, // Will set the setSigV4CredentialProvider() key
 *         aws_secret?: scalar|Param|null, // Will set the setSigV4CredentialProvider() key
 *     }>,
 * }
 * @psalm-type PimcoreStudioUiConfig = array{
 *     url_path?: scalar|Param|null, // Default: "/pimcore-studio"
 *     static_resources?: array{
 *         css?: list<scalar|Param|null>,
 *         js?: list<scalar|Param|null>,
 *         editmode?: array{
 *             css?: list<scalar|Param|null>,
 *             js?: list<scalar|Param|null>,
 *         },
 *     },
 *     wysiwyg?: array{
 *         defaultEditorConfig?: array{
 *             document?: mixed, // Default: []
 *             dataObject?: mixed, // Default: []
 *         },
 *     },
 *     csp_header?: bool|array{ // Can be used to enable or disable the Content Security Policy headers.
 *         enabled?: bool|Param, // Default: true
 *         exclude_paths?: list<scalar|Param|null>,
 *         additional_urls?: array{
 *             default-src?: list<scalar|Param|null>,
 *             img-src?: list<scalar|Param|null>,
 *             script-src?: list<scalar|Param|null>,
 *             style-src?: list<scalar|Param|null>,
 *             connect-src?: list<scalar|Param|null>,
 *             font-src?: list<scalar|Param|null>,
 *             media-src?: list<scalar|Param|null>,
 *             frame-src?: list<scalar|Param|null>,
 *         },
 *     },
 * }
 * @psalm-type PimcoreStudioBackendConfig = array{
 *     url_prefix?: scalar|Param|null, // Default: "/pimcore-studio/api"
 *     open_api_scan_paths?: list<scalar|Param|null>,
 *     api_token?: array{
 *         lifetime?: int|Param, // Default: 3600
 *     },
 *     allowed_hosts_for_cors?: list<scalar|Param|null>,
 *     security_firewall?: mixed,
 *     asset_default_formats?: array<string, array{ // Default: []
 *         resize_mode: "resize"|"scaleByWidth"|"scaleByHeight"|Param,
 *         width: int|Param,
 *         dpi: int|Param,
 *         format: "JPEG"|"PNG"|Param,
 *         quality: int|Param,
 *     }>,
 *     element_recycle_bin_threshold?: int|Param, // Default: 100
 *     mercure_settings?: array{
 *         hub_url_server?: scalar|Param|null, // The url to the mercure hub for the server.This can also be the docker container name (e.g., http://mercure/.well-known/mercure). If it is not set, default will be set to "http(s)://<YOUR_PIMCORE_MAIN_DOMAIN>/hub/.well-known/mercure". // Default: null
 *         hub_url_client?: scalar|Param|null, // The url to the mercure hub for the (frontend) client. If it is not set, the default will be set to "http(s)://<YOUR_CURRENT_PIMCORE_HOST>/hub". It is possible to use "<PIMCORE_SCHEMA_HOST>" as a placeholder for the current schema and host if path to mercure should be different. // Default: null
 *         jwt_key: scalar|Param|null, // The key used to sign the JWT token. Must be longer than 256 bits. // Default: "some-secret-default"
 *         cookie_lifetime?: int|Param, // Lifetime of the mercure cookie in seconds. Default is one hour. // Default: 3600
 *         jwt_cookie_host?: scalar|Param|null, // Domain where to set the Mercure auth cookie, e.g. ".example.com". // Default: null
 *         jwt_cookie_strictness?: bool|Param, // If true, use SameSite=Strict; if false, use SameSite=None. // Default: true
 *     },
 *     asset_download_settings?: array{
 *         size_limit?: int|Param, // The maximum size of all assets together that can be downloaded in bytes. // Default: 5368709120
 *         amount_limit?: int|Param, // The maximum amount of assets that can be downloaded at once. // Default: 1000
 *     },
 *     csv_settings?: array{
 *         default_delimiter?: scalar|Param|null, // Default delimiter to be used for csv operations. // Default: ";"
 *     },
 *     grid?: array{
 *         asset?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|Param|null,
 *                 key?: scalar|Param|null,
 *             }>,
 *         },
 *         data_object?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|Param|null,
 *                 key?: scalar|Param|null,
 *             }>,
 *             skip_field_types?: list<scalar|Param|null>,
 *         },
 *     },
 *     search_grid?: array{
 *         asset?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|Param|null,
 *                 key?: scalar|Param|null,
 *             }>,
 *         },
 *         data_object?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|Param|null,
 *                 key?: scalar|Param|null,
 *             }>,
 *         },
 *     },
 *     notes?: array{
 *         types?: array{ // List all note types for asset, document, and data-object.
 *             asset?: list<scalar|Param|null>,
 *             document?: list<scalar|Param|null>,
 *             data-object?: list<scalar|Param|null>,
 *         },
 *     },
 *     asset_metadata_adapter_mapping?: array<string, list<scalar|Param|null>>,
 *     data_object_data_adapter_mapping?: array<string, list<scalar|Param|null>>,
 *     document_type_adapter_mapping?: array<string, list<scalar|Param|null>>,
 *     user?: array{
 *         default_key_bindings?: list<array{ // Default: []
 *             key: scalar|Param|null,
 *             action: scalar|Param|null,
 *             alt?: scalar|Param|null, // Default: false
 *             ctrl?: scalar|Param|null, // Default: false
 *             shift?: scalar|Param|null, // Default: false
 *         }>,
 *     },
 *     open_api_servers?: list<array{ // Default: []
 *         url: scalar|Param|null, // The URL to the server.
 *         description: scalar|Param|null, // A description of the server.
 *     }>,
 *     widget_types?: list<scalar|Param|null>,
 *     studio_perspectives?: array<string, array{ // Default: []
 *         name: scalar|Param|null,
 *         icon: null|array{
 *             type?: "name"|"path"|Param,
 *             value?: scalar|Param|null,
 *         },
 *         widgetsLeft?: array<string, scalar|Param|null>,
 *         widgetsRight?: array<string, scalar|Param|null>,
 *         widgetsBottom?: array<string, scalar|Param|null>,
 *         expandedLeft?: scalar|Param|null, // The id of the widget that should be expanded on the left side. // Default: null
 *         expandedRight?: scalar|Param|null, // The id of the widget that should be expanded on the right side. // Default: null
 *         contextPermissions?: array<string, array<string, scalar|Param|null>>,
 *     }>,
 *     element_tree_widgets?: array<string, array{ // Default: []
 *         name: scalar|Param|null,
 *         elementType?: scalar|Param|null, // Default: "object"
 *         pageSize?: scalar|Param|null, // Default: null
 *         icon: null|array{
 *             type?: "name"|"path"|Param,
 *             value?: scalar|Param|null,
 *         },
 *         rootFolder: scalar|Param|null, // Default: "/"
 *         showRoot?: bool|Param, // Default: false
 *         classes?: list<scalar|Param|null>,
 *         pql?: scalar|Param|null, // Default: null
 *         contextPermissions?: list<scalar|Param|null>,
 *     }>,
 *     studio_from_default_email?: scalar|Param|null, // Default: "studio-admin@pimcore.com"
 *     twig?: array{ // Configure the Twig sandbox policy.
 *         sandbox_security_policy?: array{
 *             tags?: list<scalar|Param|null>,
 *             filters?: list<scalar|Param|null>,
 *             functions?: list<scalar|Param|null>,
 *         },
 *     },
 *     config_location?: array{
 *         element_tree_widgets?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *             read_target?: array{
 *                 type?: "symfony-config"|"settings-store"|Param, // Default: null
 *                 options?: list<mixed>,
 *             },
 *         },
 *         studio_perspectives?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *             read_target?: array{
 *                 type?: "symfony-config"|"settings-store"|Param, // Default: null
 *                 options?: list<mixed>,
 *             },
 *         },
 *     },
 * }
 * @psalm-type PimcoreGenericDataIndexConfig = array{
 *     index_service?: array{
 *         client_params?: array{
 *             client_name?: scalar|Param|null, // Name of search client from to be used. // Default: "default"
 *             client_type?: "openSearch"|"elasticsearch"|Param, // Type of search client to be used. // Default: "openSearch"
 *             index_prefix?: scalar|Param|null, // Default: "pimcore_"
 *         },
 *         search_settings?: array{
 *             list_page_size?: scalar|Param|null, // Default: 60
 *             list_max_filter_options?: scalar|Param|null, // Default: 500
 *             max_synchronous_children_rename_limit?: scalar|Param|null, // Maximum number of direct/synchronous children path updates if asset folders get renamed. If more then the given number of children need an path update the process will be done by the asynchronous index update command. This mechanismn is needed to be able to see directly the new paths in the folder navigation. // Default: 500
 *             search_analyzer_attributes?: array<string, array{ // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *         },
 *         index_settings?: mixed, // Default: []
 *         queue_settings?: array{
 *             worker_count?: scalar|Param|null, // Default: 1
 *             min_batch_size?: scalar|Param|null, // Default: 5
 *             max_batch_size?: scalar|Param|null, // Default: 400
 *         },
 *         system_fields_settings?: array{
 *             general?: array<string, array{ // Default: []
 *                 type: scalar|Param|null,
 *                 analyzer?: scalar|Param|null,
 *                 ignore_above?: scalar|Param|null,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             document?: array<string, array{ // Default: []
 *                 type: scalar|Param|null,
 *                 analyzer?: scalar|Param|null,
 *                 ignore_above?: scalar|Param|null,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             data_object?: array<string, array{ // Default: []
 *                 type: scalar|Param|null,
 *                 analyzer?: scalar|Param|null,
 *                 ignore_above?: scalar|Param|null,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             asset?: array<string, array{ // Default: []
 *                 type: scalar|Param|null,
 *                 analyzer?: scalar|Param|null,
 *                 ignore_above?: scalar|Param|null,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *         },
 *     },
 * }
 * @psalm-type PimcoreGenericExecutionEngineConfig = array{
 *     error_handling?: "continue_on_error"|"stop_on_first_error"|Param, // Specifies how errors should be handled for all job run executions. // Default: "continue_on_error"
 *     execution_context?: list<array{ // Default: []
 *         translations_domain?: scalar|Param|null, // Translation domain which should be used by the job run. Default value is "admin". // Default: "admin"
 *         error_handling?: "continue_on_error"|"stop_on_first_error"|Param, // Error handling behavior which should be used by the job run. Overrides the global value.
 *     }>,
 * }
 * @psalm-type PimcoreCustomReportsConfig = array{
 *     definitions?: list<array{ // Default: []
 *         id?: scalar|Param|null,
 *         name?: scalar|Param|null,
 *         niceName?: scalar|Param|null,
 *         sql?: scalar|Param|null,
 *         group?: scalar|Param|null,
 *         groupIconClass?: scalar|Param|null,
 *         iconClass?: scalar|Param|null,
 *         menuShortcut?: bool|Param,
 *         reportClass?: scalar|Param|null,
 *         chartType?: scalar|Param|null,
 *         pieColumn?: scalar|Param|null,
 *         pieLabelColumn?: scalar|Param|null,
 *         xAxis?: mixed,
 *         yAxis?: mixed,
 *         modificationDate?: int|Param,
 *         creationDate?: int|Param,
 *         shareGlobally?: bool|Param,
 *         sharedUserNames?: mixed,
 *         sharedRoleNames?: mixed,
 *         dataSourceConfig?: list<mixed>,
 *         columnConfiguration?: list<mixed>,
 *         pagination?: bool|Param,
 *     }>,
 *     adapters?: array<string, scalar|Param|null>,
 *     config_location?: array{
 *         custom_reports?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopMenuConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 * }
 * @psalm-type JmsSerializerConfig = array{
 *     twig_enabled?: scalar|Param|null, // Default: "default"
 *     profiler?: scalar|Param|null, // Default: true
 *     enum_support?: scalar|Param|null, // Default: false
 *     default_value_property_reader_support?: scalar|Param|null, // Default: false
 *     handlers?: array{
 *         datetime?: array{
 *             default_format?: scalar|Param|null, // Default: "Y-m-d\\TH:i:sP"
 *             default_deserialization_formats?: list<scalar|Param|null>,
 *             default_timezone?: scalar|Param|null, // Default: "Europe/Berlin"
 *             cdata?: scalar|Param|null, // Default: true
 *         },
 *         array_collection?: array{
 *             initialize_excluded?: bool|Param, // Default: false
 *         },
 *         symfony_uid?: array{
 *             default_format?: scalar|Param|null, // Default: "canonical"
 *             cdata?: scalar|Param|null, // Default: true
 *         },
 *     },
 *     subscribers?: array{
 *         doctrine_proxy?: array{
 *             initialize_excluded?: bool|Param, // Default: false
 *             initialize_virtual_types?: bool|Param, // Default: false
 *         },
 *     },
 *     object_constructors?: array{
 *         doctrine?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             fallback_strategy?: "null"|"exception"|"fallback"|Param, // Default: "null"
 *         },
 *     },
 *     property_naming?: string|array{
 *         id?: scalar|Param|null,
 *         separator?: scalar|Param|null, // Default: "_"
 *         lower_case?: bool|Param, // Default: true
 *     },
 *     expression_evaluator?: string|array{
 *         id?: scalar|Param|null, // Default: "jms_serializer.expression_evaluator"
 *     },
 *     metadata?: array{
 *         warmup?: array{
 *             paths?: array{
 *                 included?: list<scalar|Param|null>,
 *                 excluded?: list<scalar|Param|null>,
 *             },
 *         },
 *         cache?: scalar|Param|null, // Default: "file"
 *         debug?: bool|Param, // Default: true
 *         file_cache?: array{
 *             dir?: scalar|Param|null, // Default: null
 *         },
 *         include_interfaces?: bool|Param, // Default: false
 *         auto_detection?: bool|Param, // Default: true
 *         infer_types_from_doc_block?: bool|Param, // Default: false
 *         infer_types_from_doctrine_metadata?: bool|Param, // Infers type information from Doctrine metadata if no explicit type has been defined for a property. // Default: true
 *         directories?: array<string, array{ // Default: []
 *             path: scalar|Param|null,
 *             namespace_prefix?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     visitors?: array{
 *         json_serialization?: array{
 *             depth?: scalar|Param|null,
 *             options?: scalar|Param|null, // Default: 1024
 *         },
 *         json_deserialization?: array{
 *             options?: scalar|Param|null, // Default: 0
 *             strict?: bool|Param, // Default: false
 *         },
 *         xml_serialization?: array{
 *             version?: scalar|Param|null,
 *             encoding?: scalar|Param|null,
 *             format_output?: bool|Param, // Default: false
 *             default_root_name?: scalar|Param|null,
 *             default_root_ns?: scalar|Param|null, // Default: ""
 *         },
 *         xml_deserialization?: array{
 *             doctype_whitelist?: list<scalar|Param|null>,
 *             external_entities?: bool|Param, // Default: false
 *             options?: scalar|Param|null, // Default: 0
 *         },
 *     },
 *     default_context?: array{
 *         serialization?: string|array{
 *             id?: scalar|Param|null,
 *             serialize_null?: scalar|Param|null, // Flag if null values should be serialized
 *             enable_max_depth_checks?: scalar|Param|null, // Flag to enable the max-depth exclusion strategy
 *             attributes?: array<string, scalar|Param|null>,
 *             groups?: list<scalar|Param|null>,
 *             version?: scalar|Param|null, // Application version to use in exclusion strategies
 *         },
 *         deserialization?: string|array{
 *             id?: scalar|Param|null,
 *             serialize_null?: scalar|Param|null, // Flag if null values should be serialized
 *             enable_max_depth_checks?: scalar|Param|null, // Flag to enable the max-depth exclusion strategy
 *             attributes?: array<string, scalar|Param|null>,
 *             groups?: list<scalar|Param|null>,
 *             version?: scalar|Param|null, // Application version to use in exclusion strategies
 *         },
 *     },
 *     instances?: array<string, array{ // Default: []
 *         inherit?: bool|Param, // Default: false
 *         enum_support?: scalar|Param|null, // Default: false
 *         default_value_property_reader_support?: scalar|Param|null, // Default: false
 *         handlers?: array{
 *             datetime?: array{
 *                 default_format?: scalar|Param|null, // Default: "Y-m-d\\TH:i:sP"
 *                 default_deserialization_formats?: list<scalar|Param|null>,
 *                 default_timezone?: scalar|Param|null, // Default: "Europe/Berlin"
 *                 cdata?: scalar|Param|null, // Default: true
 *             },
 *             array_collection?: array{
 *                 initialize_excluded?: bool|Param, // Default: false
 *             },
 *             symfony_uid?: array{
 *                 default_format?: scalar|Param|null, // Default: "canonical"
 *                 cdata?: scalar|Param|null, // Default: true
 *             },
 *         },
 *         subscribers?: array{
 *             doctrine_proxy?: array{
 *                 initialize_excluded?: bool|Param, // Default: false
 *                 initialize_virtual_types?: bool|Param, // Default: false
 *             },
 *         },
 *         object_constructors?: array{
 *             doctrine?: bool|array{
 *                 enabled?: bool|Param, // Default: true
 *                 fallback_strategy?: "null"|"exception"|"fallback"|Param, // Default: "null"
 *             },
 *         },
 *         property_naming?: string|array{
 *             id?: scalar|Param|null,
 *             separator?: scalar|Param|null, // Default: "_"
 *             lower_case?: bool|Param, // Default: true
 *         },
 *         expression_evaluator?: string|array{
 *             id?: scalar|Param|null, // Default: "jms_serializer.expression_evaluator"
 *         },
 *         metadata?: array{
 *             warmup?: array{
 *                 paths?: array{
 *                     included?: list<scalar|Param|null>,
 *                     excluded?: list<scalar|Param|null>,
 *                 },
 *             },
 *             cache?: scalar|Param|null, // Default: "file"
 *             debug?: bool|Param, // Default: true
 *             file_cache?: array{
 *                 dir?: scalar|Param|null, // Default: null
 *             },
 *             include_interfaces?: bool|Param, // Default: false
 *             auto_detection?: bool|Param, // Default: true
 *             infer_types_from_doc_block?: bool|Param, // Default: false
 *             infer_types_from_doctrine_metadata?: bool|Param, // Infers type information from Doctrine metadata if no explicit type has been defined for a property. // Default: true
 *             directories?: array<string, array{ // Default: []
 *                 path: scalar|Param|null,
 *                 namespace_prefix?: scalar|Param|null, // Default: ""
 *             }>,
 *         },
 *         visitors?: array{
 *             json_serialization?: array{
 *                 depth?: scalar|Param|null,
 *                 options?: scalar|Param|null, // Default: 1024
 *             },
 *             json_deserialization?: array{
 *                 options?: scalar|Param|null, // Default: 0
 *                 strict?: bool|Param, // Default: false
 *             },
 *             xml_serialization?: array{
 *                 version?: scalar|Param|null,
 *                 encoding?: scalar|Param|null,
 *                 format_output?: bool|Param, // Default: false
 *                 default_root_name?: scalar|Param|null,
 *                 default_root_ns?: scalar|Param|null, // Default: ""
 *             },
 *             xml_deserialization?: array{
 *                 doctype_whitelist?: list<scalar|Param|null>,
 *                 external_entities?: bool|Param, // Default: false
 *                 options?: scalar|Param|null, // Default: 0
 *             },
 *         },
 *         default_context?: array{
 *             serialization?: string|array{
 *                 id?: scalar|Param|null,
 *                 serialize_null?: scalar|Param|null, // Flag if null values should be serialized
 *                 enable_max_depth_checks?: scalar|Param|null, // Flag to enable the max-depth exclusion strategy
 *                 attributes?: array<string, scalar|Param|null>,
 *                 groups?: list<scalar|Param|null>,
 *                 version?: scalar|Param|null, // Application version to use in exclusion strategies
 *             },
 *             deserialization?: string|array{
 *                 id?: scalar|Param|null,
 *                 serialize_null?: scalar|Param|null, // Flag if null values should be serialized
 *                 enable_max_depth_checks?: scalar|Param|null, // Flag to enable the max-depth exclusion strategy
 *                 attributes?: array<string, scalar|Param|null>,
 *                 groups?: list<scalar|Param|null>,
 *                 version?: scalar|Param|null, // Application version to use in exclusion strategies
 *             },
 *         },
 *     }>,
 * }
 * @psalm-type CoreShopPimcoreConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 * }
 * @psalm-type CoreShopLocaleConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 * }
 * @psalm-type CoreShopResourceConfig = array{
 *     mapping?: array{
 *         paths?: list<scalar|Param|null>,
 *     },
 *     resources?: array<string, array{ // Default: []
 *         driver?: scalar|Param|null, // Default: "doctrine/orm"
 *         options?: mixed,
 *         templates?: scalar|Param|null,
 *         classes: array{
 *             model: scalar|Param|null,
 *             interface?: scalar|Param|null,
 *             admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *             repository?: scalar|Param|null,
 *             factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *         },
 *         translation?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes: array{
 *                 model: scalar|Param|null,
 *                 interface?: scalar|Param|null,
 *                 controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|Param|null,
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     }>,
 *     pimcore?: array<string, array{ // Default: []
 *         options?: mixed,
 *         path?: array<string, scalar|Param|null>,
 *         classes?: array{
 *             model: scalar|Param|null,
 *             pimcore_class_name?: scalar|Param|null,
 *             interface?: scalar|Param|null,
 *             repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Pimcore\\PimcoreRepository"
 *             factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *             install_file?: scalar|Param|null,
 *             type?: scalar|Param|null, // Default: "object"
 *             pimcore_controller?: array<string, scalar|Param|null>,
 *         },
 *     }>,
 *     translation?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         locale_provider?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Translation\\Provider\\TranslationLocaleProviderInterface"
 *     },
 *     drivers?: list<"doctrine/orm"|Param>,
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 *     orm_cascade_merge_associations?: array<string, array{ // Default: []
 *         associations?: list<scalar|Param|null>,
 *     }>,
 * }
 * @psalm-type CoreShopSeoConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 * }
 * @psalm-type CoreShopMoneyConfig = array{
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 * }
 * @psalm-type CoreShopWorkflowConfig = array{
 *     state_machine?: array<string, array{ // Default: []
 *         places: list<scalar|Param|null>,
 *         transitions: array<string, array{ // Default: []
 *             name: scalar|Param|null,
 *             guard?: scalar|Param|null, // An expression to block the transition
 *             from?: list<scalar|Param|null>,
 *             to?: list<scalar|Param|null>,
 *         }>,
 *         place_colors?: array<string, scalar|Param|null>,
 *         transition_colors?: array<string, scalar|Param|null>,
 *         callbacks?: array{
 *             guard?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|Param|null, // Default: 0
 *                 args?: list<scalar|Param|null>,
 *             }>,
 *             before?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|Param|null, // Default: 0
 *                 args?: list<scalar|Param|null>,
 *             }>,
 *             after?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|Param|null, // Default: 0
 *                 args?: list<scalar|Param|null>,
 *             }>,
 *         },
 *     }>,
 * }
 * @psalm-type CoreShopMessengerConfig = array{
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["messenger"]
 *     },
 *     doctrine?: array{
 *         table_name?: scalar|Param|null, // Default: null
 *         connection?: scalar|Param|null, // Default: null
 *     },
 * }
 * @psalm-type CoreShopRuleConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         rule_condition?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Rule\\Model\\Condition"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Rule\\Model\\ConditionInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *             },
 *         },
 *         rule_action?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Rule\\Model\\Action"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Rule\\Model\\ActionInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 * }
 * @psalm-type CoreShopConfigurationConfig = array{
 *     resources?: array{
 *         configuration?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Configuration\\Model\\Configuration"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Configuration\\Model\\ConfigurationInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Controller\\ConfigurationController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Doctrine\\ORM\\ConfigurationRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Form\\Type\\ConfigurationType"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopOrderConfig = array{
 *     allow_order_edit?: bool|Param, // Default: false
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         cart_price_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "cart_price_rule"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\CartPriceRuleController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Form\\Type\\CartPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Form\\Type\\CartPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         cart_price_rule_voucher_code?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCode"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleVoucherRepository"
 *             },
 *         },
 *         cart_price_rule_voucher_code_customer?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeCustomer"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeCustomerInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Factory\\CartPriceRuleVoucherCodeCustomerFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleVoucherCodeCustomerRepository"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         order?: array{
 *             options?: mixed,
 *             path?: array{
 *                 order?: scalar|Param|null, // Default: "orders"
 *                 quote?: scalar|Param|null, // Default: "quotes"
 *                 cart?: scalar|Param|null, // Default: "carts"
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrder"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrder.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *                 pimcore_controller?: array{
 *                     default?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderController"
 *                     creation?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderCreationController"
 *                     edit?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderEditController"
 *                     payment?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderPaymentController"
 *                     comment?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderCommentController"
 *                     customer_creation?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\CustomerCreationController"
 *                     address_creation?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\AddressCreationController"
 *                 },
 *             },
 *         },
 *         order_item?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderItem"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderItemRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItem.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         order_invoice?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "invoices"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderInvoice"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderInvoiceRepository"
 *                 pimcore_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderInvoiceController"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoice.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         order_invoice_item?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderInvoiceItem"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoiceItem.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         order_shipment?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "shipments"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderShipment"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderShipmentRepository"
 *                 pimcore_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderShipmentController"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipment.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         order_shipment_item?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderShipmentItem"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipmentItem.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         cart_price_rule_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopProposalCartPriceRuleItem"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\ProposalCartPriceRuleItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopProposalCartPriceRuleItem.json"
 *                 type?: scalar|Param|null, // Default: "fieldcollection"
 *             },
 *         },
 *         price_rule_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopPriceRuleItem"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\PriceRuleItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopPriceRuleItem.json"
 *                 type?: scalar|Param|null, // Default: "fieldcollection"
 *             },
 *         },
 *         adjustment?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopAdjustment"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\AdjustmentInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopAdjustment.json"
 *                 type?: scalar|Param|null, // Default: "fieldcollection"
 *             },
 *         },
 *         order_item_attribute?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopOrderItemAttribute"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemAttributeInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopOrderItemAttribute.json"
 *                 type?: scalar|Param|null, // Default: "fieldcollection"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["cart_price_rule","order_list","order_detail","order_create","quote_list","quote_detail","quote_create","cart_list","cart_detail","cart_create"]
 *         install?: array{
 *             grid_config?: list<scalar|Param|null>,
 *         },
 *     },
 *     stack?: array{
 *         purchasable?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\PurchasableInterface"
 *         order?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInterface"
 *         order_item?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemInterface"
 *         order_invoice?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceInterface"
 *         order_invoice_item?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceItemInterface"
 *         order_shipment?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentInterface"
 *         order_shipment_item?: scalar|Param|null, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentItemInterface"
 *     },
 * }
 * @psalm-type CoreShopCustomerConfig = array{
 *     login_identifier?: "email"|"username"|Param, // Default: "email"
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     stack?: array{
 *         customer?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerInterface"
 *         customer_group?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerGroupInterface"
 *         company?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CompanyInterface"
 *     },
 *     pimcore?: array{
 *         company?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "companies"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopCompany"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CompanyInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CustomerBundle\\Pimcore\\Repository\\CompanyRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCompany.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         customer?: array{
 *             options?: mixed,
 *             path?: array{
 *                 customer?: scalar|Param|null, // Default: "customers"
 *                 guest?: scalar|Param|null, // Default: "guests"
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopCustomer"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CustomerBundle\\Pimcore\\Repository\\CustomerRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomer.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         customer_group?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "customer_groups"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopCustomerGroup"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerGroupInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomerGroup.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["customer_list","customer_group_list"]
 *         install?: array{
 *             grid_config?: list<scalar|Param|null>,
 *         },
 *     },
 * }
 * @psalm-type CoreShopUserConfig = array{
 *     driver?: scalar|Param|null, // Default: "doctrine/orm"
 *     stack?: array{
 *         user?: scalar|Param|null, // Default: "CoreShop\\Component\\User\\Model\\UserInterface"
 *     },
 *     pimcore?: array{
 *         user?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "user"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopUser"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\User\\Model\\UserInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\UserBundle\\Pimcore\\Repository\\UserRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopUserBundle/Resources/install/pimcore/classes/CoreShopUser.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 * }
 * @psalm-type CoreShopInventoryConfig = array{
 *     checker?: scalar|Param|null, // Default: "CoreShop\\Component\\Inventory\\Checker\\AvailabilityChecker"
 * }
 * @psalm-type CoreShopVariantConfig = array{
 *     stack?: array{
 *         attribute_group?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeGroupInterface"
 *         attribute?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeInterface"
 *         variant_aware?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\ProductVariantAwareInterface"
 *     },
 *     pimcore?: array{
 *         attribute_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeGroup"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeGroupInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeGroup.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         attribute_value?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeValue"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeValueInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeValue.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         attribute_color?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeColor"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeColorInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeColor.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 *     redirect_to_main_variant?: scalar|Param|null, // Default: true
 * }
 * @psalm-type CoreShopProductConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         product_price_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "product_price_rule"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Controller\\ProductPriceRuleController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductPriceRuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         product_specific_price_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductSpecificPriceRuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductSpecificPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductSpecificPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         product_unit?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "product_unit"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnit"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductUnitRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\Unit\\ProductUnitType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\Unit\\ProductUnitTranslationType"
 *                 },
 *             },
 *         },
 *         product_unit_definitions?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitions"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionsInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Controller\\ProductUnitDefinitionsController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductUnitDefinitionsRepository"
 *             },
 *         },
 *         product_unit_definition?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinition"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *         product_unit_definition_price?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionPrice"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionPriceInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         product?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "products"
 *             slug?: bool|Param, // Default: true
 *             route?: array{
 *                 name?: scalar|Param|null, // Default: "coreshop_product_detail"
 *                 id_param?: scalar|Param|null, // Default: "product"
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopProduct"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Pimcore\\Repository\\ProductRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopProduct.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         category?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "categories"
 *             slug?: bool|Param, // Default: true
 *             route?: array{
 *                 name?: scalar|Param|null, // Default: "coreshop_category_list"
 *                 id_param?: scalar|Param|null, // Default: "category"
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopCategory"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\CategoryInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductBundle\\Pimcore\\Repository\\CategoryRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopCategory.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         manufacturer?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "manufacturers"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopManufacturer"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ManufacturerInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopManufacturer.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["product_price_rule","product_unit"]
 *     },
 *     stack?: array{
 *         product?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ProductInterface"
 *         category?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\CategoryInterface"
 *         manufacturer?: scalar|Param|null, // Default: "CoreShop\\Component\\Product\\Model\\ManufacturerInterface"
 *     },
 * }
 * @psalm-type CoreShopThemeConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     default_resolvers?: array{
 *         pimcore_site?: bool|Param, // Default: false
 *         pimcore_document_property?: bool|Param, // Default: false
 *     },
 * }
 * @psalm-type CoreShopAddressConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     stack?: array{
 *         address?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\AddressInterface"
 *     },
 *     resources?: array{
 *         country?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "country"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\Country"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\CountryInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Controller\\CountryController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Doctrine\\ORM\\CountryRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\CountryType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\CountryTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\CountryTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\CountryTranslationType"
 *                 },
 *             },
 *         },
 *         zone?: array{
 *             permission?: scalar|Param|null, // Default: "zone"
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\Zone"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\ZoneInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\ZoneType"
 *             },
 *         },
 *         state?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "state"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\State"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\StateInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\StateType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\StateTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\StateTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\StateTranslationType"
 *                 },
 *             },
 *         },
 *         address_identifier?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "address_identifier"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\AddressIdentifier"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\AddressIdentifierInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Doctrine\\ORM\\AddressIdentifierRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\AddressIdentifierType"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         address?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "addresses"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopAddress"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Address\\Model\\AddressInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopAddressBundle/Resources/install/pimcore/classes/CoreShopAddress.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["country","state","zone"]
 *     },
 * }
 * @psalm-type CoreShopCurrencyConfig = array{
 *     money_decimal_factor?: int|Param, // Default: 100
 *     money_decimal_precision?: int|Param, // Default: 2
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         currency?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             permission?: scalar|Param|null, // Default: "currency"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Currency\\Model\\Currency"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Currency\\Model\\CurrencyInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Controller\\CurrencyController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Doctrine\\ORM\\CurrencyRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Form\\Type\\CurrencyType"
 *             },
 *         },
 *         exchange_rate?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "exchange_rate"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Currency\\Model\\ExchangeRate"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Currency\\Model\\ExchangeRateInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Controller\\ExchangeRateController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Doctrine\\ORM\\ExchangeRateRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Form\\Type\\ExchangeRateType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["currency","exchange_rate"]
 *     },
 * }
 * @psalm-type CoreShopTaxationConfig = array{
 *     resources?: array{
 *         tax_rate?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "tax_rate"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRate"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Doctrine\\ORM\\TaxRateRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRateType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRateTranslationType"
 *                 },
 *             },
 *         },
 *         tax_rule_group?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "tax_rule_group"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleGroup"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleGroupInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Controller\\TaxRuleGroupController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRuleGroupType"
 *             },
 *         },
 *         tax_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Doctrine\\ORM\\TaxRuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRuleType"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         tax_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopTaxItem"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null,
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopTaxationBundle/Resources/install/pimcore/fieldcollections/CoreShopTaxItem.json"
 *                 type?: scalar|Param|null, // Default: "fieldcollection"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["tax_rate","tax_rule_group"]
 *     },
 * }
 * @psalm-type CoreShopStoreConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         store?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "store"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Store\\Model\\Store"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Store\\Model\\StoreInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\StoreBundle\\Controller\\StoreController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\StoreBundle\\Doctrine\\ORM\\StoreRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\StoreBundle\\Form\\Type\\StoreType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["store"]
 *     },
 * }
 * @psalm-type CoreShopIndexConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     mysql_auto_generate_migrations?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         index?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "index"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\Index"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\IndexInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Controller\\IndexController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\IndexType"
 *             },
 *         },
 *         index_column?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\IndexColumn"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\IndexColumnInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\IndexColumnType"
 *             },
 *         },
 *         filter?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "filter"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\Filter"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\FilterInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Controller\\FilterController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\FilterType"
 *             },
 *         },
 *         filter_condition?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\FilterCondition"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Index\\Model\\FilterConditionInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\FilterConditionType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["index","filter"]
 *     },
 *     mapping_types?: array<string, scalar|Param|null>,
 *     worker_mapping_types?: array<string, array<string, scalar|Param|null>>,
 * }
 * @psalm-type CoreShopShippingConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     default_resolver?: scalar|Param|null,
 *     resources?: array{
 *         carrier?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "carrier"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\Carrier"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Controller\\CarrierController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\CarrierType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\CarrierTranslationType"
 *                 },
 *             },
 *         },
 *         shipping_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "shipping_rule"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Controller\\ShippingRuleController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\RuleBundle\\Doctrine\\ORM\\RuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\ShippingRuleType"
 *             },
 *         },
 *         shipping_rule_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleGroup"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleGroupInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\ShippingRuleGroupType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["carrier","shipping_rule"]
 *     },
 * }
 * @psalm-type CoreShopPaymentConfig = array{
 *     driver?: scalar|Param|null, // Default: "doctrine/orm"
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         payment_provider?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "payment_provider"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProvider"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Doctrine\\ORM\\PaymentProviderRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderTranslationType"
 *                 },
 *             },
 *         },
 *         payment?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\Payment"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Doctrine\\ORM\\PaymentRepository"
 *             },
 *         },
 *         payment_provider_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "payment_provider_rule"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Controller\\PaymentProviderRuleController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\RuleBundle\\Doctrine\\ORM\\RuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleTranslation"
 *                     interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleTranslationInterface"
 *                     repository?: scalar|Param|null,
 *                     factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleTranslationType"
 *                 },
 *             },
 *         },
 *         payment_provider_rule_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleGroup"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleGroupInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleGroupType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["payment_provider","payment_provider_rule"]
 *     },
 * }
 * @psalm-type CoreShopSequenceConfig = array{
 *     resources?: array{
 *         sequence?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Sequence\\Model\\Sequence"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Sequence\\Model\\SequenceInterface"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Sequence\\Factory\\SequenceFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\SequenceBundle\\Doctrine\\ORM\\SequenceRepository"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopPayumPaymentConfig = array{
 *     driver?: scalar|Param|null, // Default: "doctrine/orm"
 *     resources?: array{
 *         gateway_config?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\PayumPayment\\Model\\GatewayConfig"
 *                 controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *         payment_security_token?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\PayumPayment\\Model\\PaymentSecurityToken"
 *                 controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopNotificationConfig = array{
 *     resources?: array{
 *         notification_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|Param|null, // Default: "notification"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Notification\\Model\\NotificationRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Notification\\Model\\NotificationRuleInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\NotificationBundle\\Doctrine\\ORM\\NotificationRuleRepository"
 *                 admin_controller?: scalar|Param|null, // Default: "CoreShop\\Bundle\\NotificationBundle\\Controller\\NotificationRuleController"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\NotificationBundle\\Form\\Type\\NotificationRuleType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["notification"]
 *     },
 * }
 * @psalm-type CoreShopTrackingConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     trackers?: array<string, array{ // Default: []
 *         enabled?: bool|Param, // Default: false
 *     }>,
 * }
 * @psalm-type CoreShopFrontendConfig = array{
 *     view_suffix?: scalar|Param|null, // Default: "twig"
 *     view_bundle?: scalar|Param|null, // Deprecated: Use view_prefix instead // Default: "@CoreShopFrontend"
 *     view_prefix?: scalar|Param|null, // Default: "@CoreShopFrontend"
 *     category?: array{
 *         valid_sort_options?: list<scalar|Param|null>,
 *         default_sort_name?: scalar|Param|null, // Default: "name"
 *         default_sort_direction?: scalar|Param|null, // Default: "asc"
 *     },
 *     pimcore_admin?: array{
 *         install?: array{
 *             routes?: list<scalar|Param|null>,
 *             documents?: list<scalar|Param|null>,
 *             image_thumbnails?: list<scalar|Param|null>,
 *         },
 *     },
 *     controllers?: array{
 *         index?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\IndexController"
 *         register?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\RegisterController"
 *         customer?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CustomerController"
 *         currency?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CurrencyController"
 *         search?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\SearchController"
 *         cart?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CartController"
 *         checkout?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CheckoutController"
 *         order?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\OrderController"
 *         category?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CategoryController"
 *         product?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\ProductController"
 *         quote?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\QuoteController"
 *         security?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\SecurityController"
 *         payment?: scalar|Param|null, // Default: "CoreShop\\Bundle\\PayumBundle\\Controller\\PaymentController"
 *         mail?: scalar|Param|null, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\MailController"
 *     },
 * }
 * @psalm-type CoreShopPayumConfig = array{
 *     template?: array{
 *         layout?: scalar|Param|null, // Default: "@CoreShopPayum/:layout.html.twig"
 *         obtain_credit_card?: scalar|Param|null, // Default: "@CoreShopPayum/Action/obtainCreditCard.html.twig"
 *     },
 * }
 * @psalm-type CoreShopProductQuantityPriceRulesConfig = array{
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     action_constraints?: list<array{ // Default: []
 *         class?: scalar|Param|null,
 *         groups?: array<string, scalar|Param|null>,
 *     }>,
 *     resources?: array{
 *         product_quantity_price_rule_range?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\QuantityRange"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\QuantityRangeInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null,
 *             },
 *         },
 *         product_quantity_price_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\ProductQuantityPriceRule"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\ProductQuantityPriceRuleInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductQuantityPriceRulesBundle\\Doctrine\\ORM\\ProductQuantityPriceRuleRepository"
 *                 form?: scalar|Param|null, // Default: "CoreShop\\Bundle\\ProductQuantityPriceRulesBundle\\Form\\Type\\ProductQuantityPriceRuleType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array<string, scalar|Param|null>,
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *         permissions?: scalar|Param|null, // Default: ["notification"]
 *     },
 * }
 * @psalm-type CoreShopWishlistConfig = array{
 *     pimcore?: array{
 *         wishlist?: array{
 *             options?: mixed,
 *             path?: array{
 *                 wishlist?: scalar|Param|null, // Default: "wishlists"
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopWishlist"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\WishlistBundle\\Pimcore\\Repository\\WishlistRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlist.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *         wishlist_item?: array{
 *             options?: mixed,
 *             path?: scalar|Param|null, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "Pimcore\\Model\\DataObject\\CoreShopWishlistItem"
 *                 pimcore_class_name?: scalar|Param|null,
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistItemInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\WishlistBundle\\Pimcore\\Repository\\WishlistItemRepository"
 *                 install_file?: scalar|Param|null, // Default: "@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlistItem.json"
 *                 type?: scalar|Param|null, // Default: "object"
 *             },
 *         },
 *     },
 *     stack?: array{
 *         wishlist?: scalar|Param|null, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistInterface"
 *         wishlist_item?: scalar|Param|null, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistItemInterface"
 *         wishlist_product?: scalar|Param|null, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistProductInterface"
 *     },
 * }
 * @psalm-type CoreShopClassDefinitionPatchConfig = array{
 *     patches?: array<string, array{ // Default: []
 *         interface?: list<scalar|Param|null>,
 *         parent_class?: scalar|Param|null,
 *         group?: scalar|Param|null,
 *         description?: scalar|Param|null,
 *         listing_parent_class?: scalar|Param|null,
 *         use_traits?: list<scalar|Param|null>,
 *         listing_use_traits?: list<scalar|Param|null>,
 *         fields?: array<string, array{ // Default: []
 *             after?: scalar|Param|null,
 *             before?: scalar|Param|null,
 *             replace?: bool|Param, // Default: true
 *             definition?: mixed,
 *         }>,
 *     }>,
 * }
 * @psalm-type PayumConfig = array{
 *     security: array{
 *         token_storage: array<string, array{ // Default: []
 *             filesystem?: array{
 *                 storage_dir: scalar|Param|null,
 *                 id_property?: scalar|Param|null, // Default: null
 *             },
 *             doctrine?: string|array{
 *                 driver: scalar|Param|null,
 *             },
 *             custom?: string|array{
 *                 service: scalar|Param|null,
 *             },
 *             propel1?: array<mixed>,
 *             propel2?: array<mixed>,
 *         }>,
 *     },
 *     dynamic_gateways?: array{
 *         sonata_admin?: bool|Param, // Default: false
 *         config_storage: array<string, array{ // Default: []
 *             filesystem?: array{
 *                 storage_dir: scalar|Param|null,
 *                 id_property?: scalar|Param|null, // Default: null
 *             },
 *             doctrine?: string|array{
 *                 driver: scalar|Param|null,
 *             },
 *             custom?: string|array{
 *                 service: scalar|Param|null,
 *             },
 *             propel1?: array<mixed>,
 *             propel2?: array<mixed>,
 *         }>,
 *         encryption?: array{
 *             defuse_secret_key?: scalar|Param|null,
 *         },
 *     },
 *     gateways?: array<string, mixed>,
 *     storages?: array<string, array{ // Default: []
 *         extension?: array{
 *             all?: bool|Param, // Default: true
 *             gateways?: array<string, scalar|Param|null>,
 *             factories?: array<string, scalar|Param|null>,
 *         },
 *         filesystem?: array{
 *             storage_dir: scalar|Param|null,
 *             id_property?: scalar|Param|null, // Default: null
 *         },
 *         doctrine?: string|array{
 *             driver: scalar|Param|null,
 *         },
 *         custom?: string|array{
 *             service: scalar|Param|null,
 *         },
 *         propel1?: array<mixed>,
 *         propel2?: array<mixed>,
 *     }>,
 * }
 * @psalm-type StofDoctrineExtensionsConfig = array{
 *     orm?: array<string, array{ // Default: []
 *         translatable?: scalar|Param|null, // Default: false
 *         timestampable?: scalar|Param|null, // Default: false
 *         blameable?: scalar|Param|null, // Default: false
 *         sluggable?: scalar|Param|null, // Default: false
 *         tree?: scalar|Param|null, // Default: false
 *         loggable?: scalar|Param|null, // Default: false
 *         ip_traceable?: scalar|Param|null, // Default: false
 *         sortable?: scalar|Param|null, // Default: false
 *         softdeleteable?: scalar|Param|null, // Default: false
 *         uploadable?: scalar|Param|null, // Default: false
 *         reference_integrity?: scalar|Param|null, // Default: false
 *     }>,
 *     mongodb?: array<string, array{ // Default: []
 *         translatable?: scalar|Param|null, // Default: false
 *         timestampable?: scalar|Param|null, // Default: false
 *         blameable?: scalar|Param|null, // Default: false
 *         sluggable?: scalar|Param|null, // Default: false
 *         tree?: scalar|Param|null, // Default: false
 *         loggable?: scalar|Param|null, // Default: false
 *         ip_traceable?: scalar|Param|null, // Default: false
 *         sortable?: scalar|Param|null, // Default: false
 *         softdeleteable?: scalar|Param|null, // Default: false
 *         uploadable?: scalar|Param|null, // Default: false
 *         reference_integrity?: scalar|Param|null, // Default: false
 *     }>,
 *     class?: array{
 *         translatable?: scalar|Param|null, // Default: "Gedmo\\Translatable\\TranslatableListener"
 *         timestampable?: scalar|Param|null, // Default: "Gedmo\\Timestampable\\TimestampableListener"
 *         blameable?: scalar|Param|null, // Default: "Gedmo\\Blameable\\BlameableListener"
 *         sluggable?: scalar|Param|null, // Default: "Gedmo\\Sluggable\\SluggableListener"
 *         tree?: scalar|Param|null, // Default: "Gedmo\\Tree\\TreeListener"
 *         loggable?: scalar|Param|null, // Default: "Gedmo\\Loggable\\LoggableListener"
 *         sortable?: scalar|Param|null, // Default: "Gedmo\\Sortable\\SortableListener"
 *         softdeleteable?: scalar|Param|null, // Default: "Gedmo\\SoftDeleteable\\SoftDeleteableListener"
 *         uploadable?: scalar|Param|null, // Default: "Gedmo\\Uploadable\\UploadableListener"
 *         reference_integrity?: scalar|Param|null, // Default: "Gedmo\\ReferenceIntegrity\\ReferenceIntegrityListener"
 *     },
 *     softdeleteable?: array{
 *         handle_post_flush_event?: bool|Param, // Default: false
 *     },
 *     uploadable?: array{
 *         default_file_path?: scalar|Param|null, // Default: null
 *         mime_type_guesser_class?: scalar|Param|null, // Default: "Stof\\DoctrineExtensionsBundle\\Uploadable\\MimeTypeGuesserAdapter"
 *         default_file_info_class?: scalar|Param|null, // Default: "Stof\\DoctrineExtensionsBundle\\Uploadable\\UploadedFileInfo"
 *         validate_writable_directory?: bool|Param, // Default: true
 *     },
 *     default_locale?: scalar|Param|null, // Default: "en"
 *     translation_fallback?: bool|Param, // Default: false
 *     persist_default_translation?: bool|Param, // Default: false
 *     skip_translation_on_load?: bool|Param, // Default: false
 *     metadata_cache_pool?: scalar|Param|null, // Default: null
 * }
 * @psalm-type SyliusThemeConfig = array{
 *     sources?: array{
 *         filesystem?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             filename?: scalar|Param|null, // Default: "composer.json"
 *             scan_depth?: scalar|Param|null, // Restrict depth to scan for configuration file inside theme folder // Default: 1
 *             directories?: list<scalar|Param|null>,
 *         },
 *         test?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *         },
 *     },
 *     assets?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     templating?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     translations?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     context?: scalar|Param|null, // Default: "sylius.theme.context.settable"
 *     legacy_mode?: bool|Param, // Deprecated: "legacy_mode" at path "sylius_theme.legacy_mode" is deprecated since Sylius/ThemeBundle 2.0 and will be removed in 3.0. // Default: false
 * }
 * @psalm-type PimcoreGoogleMarketingConfig = array{
 *     client_id?: scalar|Param|null, // This is required for the Google API integrations. Only use a `Service Account´ from the Google Cloud Console. // Default: null
 *     email?: scalar|Param|null, // Email address of the Google service account // Default: null
 *     simple_api_key?: scalar|Param|null, // Server API key // Default: null
 *     browser_api_key?: scalar|Param|null, // Browser API key // Default: null
 * }
 * @psalm-type FrameworkConfig = array{
 *     secret?: scalar|Param|null,
 *     http_method_override?: bool|Param, // Set true to enable support for the '_method' request parameter to determine the intended HTTP method on POST requests. // Default: false
 *     allowed_http_method_override?: list<string|Param>|null,
 *     trust_x_sendfile_type_header?: scalar|Param|null, // Set true to enable support for xsendfile in binary file responses. // Default: "%env(bool:default::SYMFONY_TRUST_X_SENDFILE_TYPE_HEADER)%"
 *     ide?: scalar|Param|null, // Default: "%env(default::SYMFONY_IDE)%"
 *     test?: bool|Param,
 *     default_locale?: scalar|Param|null, // Default: "en"
 *     set_locale_from_accept_language?: bool|Param, // Whether to use the Accept-Language HTTP header to set the Request locale (only when the "_locale" request attribute is not passed). // Default: false
 *     set_content_language_from_locale?: bool|Param, // Whether to set the Content-Language HTTP header on the Response using the Request locale. // Default: false
 *     enabled_locales?: list<scalar|Param|null>,
 *     trusted_hosts?: list<scalar|Param|null>,
 *     trusted_proxies?: mixed, // Default: ["%env(default::SYMFONY_TRUSTED_PROXIES)%"]
 *     trusted_headers?: list<scalar|Param|null>,
 *     error_controller?: scalar|Param|null, // Default: "error_controller"
 *     handle_all_throwables?: bool|Param, // HttpKernel will handle all kinds of \Throwable. // Default: true
 *     csrf_protection?: bool|array{
 *         enabled?: scalar|Param|null, // Default: null
 *         stateless_token_ids?: list<scalar|Param|null>,
 *         check_header?: scalar|Param|null, // Whether to check the CSRF token in a header in addition to a cookie when using stateless protection. // Default: false
 *         cookie_name?: scalar|Param|null, // The name of the cookie to use when using stateless protection. // Default: "csrf-token"
 *     },
 *     form?: bool|array{ // Form configuration
 *         enabled?: bool|Param, // Default: true
 *         csrf_protection?: bool|array{
 *             enabled?: scalar|Param|null, // Default: null
 *             token_id?: scalar|Param|null, // Default: null
 *             field_name?: scalar|Param|null, // Default: "_token"
 *             field_attr?: array<string, scalar|Param|null>,
 *         },
 *     },
 *     http_cache?: bool|array{ // HTTP cache configuration
 *         enabled?: bool|Param, // Default: false
 *         debug?: bool|Param, // Default: "%kernel.debug%"
 *         trace_level?: "none"|"short"|"full"|Param,
 *         trace_header?: scalar|Param|null,
 *         default_ttl?: int|Param,
 *         private_headers?: list<scalar|Param|null>,
 *         skip_response_headers?: list<scalar|Param|null>,
 *         allow_reload?: bool|Param,
 *         allow_revalidate?: bool|Param,
 *         stale_while_revalidate?: int|Param,
 *         stale_if_error?: int|Param,
 *         terminate_on_cache_hit?: bool|Param,
 *     },
 *     esi?: bool|array{ // ESI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     ssi?: bool|array{ // SSI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     fragments?: bool|array{ // Fragments configuration
 *         enabled?: bool|Param, // Default: false
 *         hinclude_default_template?: scalar|Param|null, // Default: null
 *         path?: scalar|Param|null, // Default: "/_fragment"
 *     },
 *     profiler?: bool|array{ // Profiler configuration
 *         enabled?: bool|Param, // Default: false
 *         collect?: bool|Param, // Default: true
 *         collect_parameter?: scalar|Param|null, // The name of the parameter to use to enable or disable collection on a per request basis. // Default: null
 *         only_exceptions?: bool|Param, // Default: false
 *         only_main_requests?: bool|Param, // Default: false
 *         dsn?: scalar|Param|null, // Default: "file:%kernel.cache_dir%/profiler"
 *         collect_serializer_data?: bool|Param, // Enables the serializer data collector and profiler panel. // Default: false
 *     },
 *     workflows?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         workflows?: array<string, array{ // Default: []
 *             audit_trail?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *             },
 *             type?: "workflow"|"state_machine"|Param, // Default: "state_machine"
 *             marking_store?: array{
 *                 type?: "method"|Param,
 *                 property?: scalar|Param|null,
 *                 service?: scalar|Param|null,
 *             },
 *             supports?: list<scalar|Param|null>,
 *             definition_validators?: list<scalar|Param|null>,
 *             support_strategy?: scalar|Param|null,
 *             initial_marking?: list<scalar|Param|null>,
 *             events_to_dispatch?: list<string|Param>|null,
 *             places?: list<array{ // Default: []
 *                 name: scalar|Param|null,
 *                 metadata?: list<mixed>,
 *             }>,
 *             transitions: list<array{ // Default: []
 *                 name: string|Param,
 *                 guard?: string|Param, // An expression to block the transition.
 *                 from?: list<array{ // Default: []
 *                     place: string|Param,
 *                     weight?: int|Param, // Default: 1
 *                 }>,
 *                 to?: list<array{ // Default: []
 *                     place: string|Param,
 *                     weight?: int|Param, // Default: 1
 *                 }>,
 *                 weight?: int|Param, // Default: 1
 *                 metadata?: list<mixed>,
 *             }>,
 *             metadata?: list<mixed>,
 *         }>,
 *     },
 *     router?: bool|array{ // Router configuration
 *         enabled?: bool|Param, // Default: false
 *         resource: scalar|Param|null,
 *         type?: scalar|Param|null,
 *         cache_dir?: scalar|Param|null, // Deprecated: Setting the "framework.router.cache_dir.cache_dir" configuration option is deprecated. It will be removed in version 8.0. // Default: "%kernel.build_dir%"
 *         default_uri?: scalar|Param|null, // The default URI used to generate URLs in a non-HTTP context. // Default: null
 *         http_port?: scalar|Param|null, // Default: 80
 *         https_port?: scalar|Param|null, // Default: 443
 *         strict_requirements?: scalar|Param|null, // set to true to throw an exception when a parameter does not match the requirements set to false to disable exceptions when a parameter does not match the requirements (and return null instead) set to null to disable parameter checks against requirements 'true' is the preferred configuration in development mode, while 'false' or 'null' might be preferred in production // Default: true
 *         utf8?: bool|Param, // Default: true
 *     },
 *     session?: bool|array{ // Session configuration
 *         enabled?: bool|Param, // Default: false
 *         storage_factory_id?: scalar|Param|null, // Default: "session.storage.factory.native"
 *         handler_id?: scalar|Param|null, // Defaults to using the native session handler, or to the native *file* session handler if "save_path" is not null.
 *         name?: scalar|Param|null,
 *         cookie_lifetime?: scalar|Param|null,
 *         cookie_path?: scalar|Param|null,
 *         cookie_domain?: scalar|Param|null,
 *         cookie_secure?: true|false|"auto"|Param, // Default: "auto"
 *         cookie_httponly?: bool|Param, // Default: true
 *         cookie_samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *         use_cookies?: bool|Param,
 *         gc_divisor?: scalar|Param|null,
 *         gc_probability?: scalar|Param|null,
 *         gc_maxlifetime?: scalar|Param|null,
 *         save_path?: scalar|Param|null, // Defaults to "%kernel.cache_dir%/sessions" if the "handler_id" option is not null.
 *         metadata_update_threshold?: int|Param, // Seconds to wait between 2 session metadata updates. // Default: 0
 *         sid_length?: int|Param, // Deprecated: Setting the "framework.session.sid_length.sid_length" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *         sid_bits_per_character?: int|Param, // Deprecated: Setting the "framework.session.sid_bits_per_character.sid_bits_per_character" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *     },
 *     request?: bool|array{ // Request configuration
 *         enabled?: bool|Param, // Default: false
 *         formats?: array<string, string|list<scalar|Param|null>>,
 *     },
 *     assets?: bool|array{ // Assets configuration
 *         enabled?: bool|Param, // Default: true
 *         strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *         version_strategy?: scalar|Param|null, // Default: null
 *         version?: scalar|Param|null, // Default: null
 *         version_format?: scalar|Param|null, // Default: "%%s?%%s"
 *         json_manifest_path?: scalar|Param|null, // Default: null
 *         base_path?: scalar|Param|null, // Default: ""
 *         base_urls?: list<scalar|Param|null>,
 *         packages?: array<string, array{ // Default: []
 *             strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *             version_strategy?: scalar|Param|null, // Default: null
 *             version?: scalar|Param|null,
 *             version_format?: scalar|Param|null, // Default: null
 *             json_manifest_path?: scalar|Param|null, // Default: null
 *             base_path?: scalar|Param|null, // Default: ""
 *             base_urls?: list<scalar|Param|null>,
 *         }>,
 *     },
 *     asset_mapper?: bool|array{ // Asset Mapper configuration
 *         enabled?: bool|Param, // Default: false
 *         paths?: array<string, scalar|Param|null>,
 *         excluded_patterns?: list<scalar|Param|null>,
 *         exclude_dotfiles?: bool|Param, // If true, any files starting with "." will be excluded from the asset mapper. // Default: true
 *         server?: bool|Param, // If true, a "dev server" will return the assets from the public directory (true in "debug" mode only by default). // Default: true
 *         public_prefix?: scalar|Param|null, // The public path where the assets will be written to (and served from when "server" is true). // Default: "/assets/"
 *         missing_import_mode?: "strict"|"warn"|"ignore"|Param, // Behavior if an asset cannot be found when imported from JavaScript or CSS files - e.g. "import './non-existent.js'". "strict" means an exception is thrown, "warn" means a warning is logged, "ignore" means the import is left as-is. // Default: "warn"
 *         extensions?: array<string, scalar|Param|null>,
 *         importmap_path?: scalar|Param|null, // The path of the importmap.php file. // Default: "%kernel.project_dir%/importmap.php"
 *         importmap_polyfill?: scalar|Param|null, // The importmap name that will be used to load the polyfill. Set to false to disable. // Default: "es-module-shims"
 *         importmap_script_attributes?: array<string, scalar|Param|null>,
 *         vendor_dir?: scalar|Param|null, // The directory to store JavaScript vendors. // Default: "%kernel.project_dir%/assets/vendor"
 *         precompress?: bool|array{ // Precompress assets with Brotli, Zstandard and gzip.
 *             enabled?: bool|Param, // Default: false
 *             formats?: list<scalar|Param|null>,
 *             extensions?: list<scalar|Param|null>,
 *         },
 *     },
 *     translator?: bool|array{ // Translator configuration
 *         enabled?: bool|Param, // Default: true
 *         fallbacks?: list<scalar|Param|null>,
 *         logging?: bool|Param, // Default: false
 *         formatter?: scalar|Param|null, // Default: "translator.formatter.default"
 *         cache_dir?: scalar|Param|null, // Default: "%kernel.cache_dir%/translations"
 *         default_path?: scalar|Param|null, // The default path used to load translations. // Default: "%kernel.project_dir%/translations"
 *         paths?: list<scalar|Param|null>,
 *         pseudo_localization?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             accents?: bool|Param, // Default: true
 *             expansion_factor?: float|Param, // Default: 1.0
 *             brackets?: bool|Param, // Default: true
 *             parse_html?: bool|Param, // Default: false
 *             localizable_html_attributes?: list<scalar|Param|null>,
 *         },
 *         providers?: array<string, array{ // Default: []
 *             dsn?: scalar|Param|null,
 *             domains?: list<scalar|Param|null>,
 *             locales?: list<scalar|Param|null>,
 *         }>,
 *         globals?: array<string, string|array{ // Default: []
 *             value?: mixed,
 *             message?: string|Param,
 *             parameters?: array<string, scalar|Param|null>,
 *             domain?: string|Param,
 *         }>,
 *     },
 *     validation?: bool|array{ // Validation configuration
 *         enabled?: bool|Param, // Default: true
 *         cache?: scalar|Param|null, // Deprecated: Setting the "framework.validation.cache.cache" configuration option is deprecated. It will be removed in version 8.0.
 *         enable_attributes?: bool|Param, // Default: true
 *         static_method?: list<scalar|Param|null>,
 *         translation_domain?: scalar|Param|null, // Default: "validators"
 *         email_validation_mode?: "html5"|"html5-allow-no-tld"|"strict"|"loose"|Param, // Default: "html5"
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         not_compromised_password?: bool|array{
 *             enabled?: bool|Param, // When disabled, compromised passwords will be accepted as valid. // Default: true
 *             endpoint?: scalar|Param|null, // API endpoint for the NotCompromisedPassword Validator. // Default: null
 *         },
 *         disable_translation?: bool|Param, // Default: false
 *         auto_mapping?: array<string, array{ // Default: []
 *             services?: list<scalar|Param|null>,
 *         }>,
 *     },
 *     annotations?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     serializer?: bool|array{ // Serializer configuration
 *         enabled?: bool|Param, // Default: true
 *         enable_attributes?: bool|Param, // Default: true
 *         name_converter?: scalar|Param|null,
 *         circular_reference_handler?: scalar|Param|null,
 *         max_depth_handler?: scalar|Param|null,
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         default_context?: list<mixed>,
 *         named_serializers?: array<string, array{ // Default: []
 *             name_converter?: scalar|Param|null,
 *             default_context?: list<mixed>,
 *             include_built_in_normalizers?: bool|Param, // Whether to include the built-in normalizers // Default: true
 *             include_built_in_encoders?: bool|Param, // Whether to include the built-in encoders // Default: true
 *         }>,
 *     },
 *     property_access?: bool|array{ // Property access configuration
 *         enabled?: bool|Param, // Default: true
 *         magic_call?: bool|Param, // Default: false
 *         magic_get?: bool|Param, // Default: true
 *         magic_set?: bool|Param, // Default: true
 *         throw_exception_on_invalid_index?: bool|Param, // Default: false
 *         throw_exception_on_invalid_property_path?: bool|Param, // Default: true
 *     },
 *     type_info?: bool|array{ // Type info configuration
 *         enabled?: bool|Param, // Default: true
 *         aliases?: array<string, scalar|Param|null>,
 *     },
 *     property_info?: bool|array{ // Property info configuration
 *         enabled?: bool|Param, // Default: true
 *         with_constructor_extractor?: bool|Param, // Registers the constructor extractor.
 *     },
 *     cache?: array{ // Cache configuration
 *         prefix_seed?: scalar|Param|null, // Used to namespace cache keys when using several apps with the same shared backend. // Default: "_%kernel.project_dir%.%kernel.container_class%"
 *         app?: scalar|Param|null, // App related cache pools configuration. // Default: "cache.adapter.filesystem"
 *         system?: scalar|Param|null, // System related cache pools configuration. // Default: "cache.adapter.system"
 *         directory?: scalar|Param|null, // Default: "%kernel.share_dir%/pools/app"
 *         default_psr6_provider?: scalar|Param|null,
 *         default_redis_provider?: scalar|Param|null, // Default: "redis://localhost"
 *         default_valkey_provider?: scalar|Param|null, // Default: "valkey://localhost"
 *         default_memcached_provider?: scalar|Param|null, // Default: "memcached://localhost"
 *         default_doctrine_dbal_provider?: scalar|Param|null, // Default: "database_connection"
 *         default_pdo_provider?: scalar|Param|null, // Default: null
 *         pools?: array<string, array{ // Default: []
 *             adapters?: list<scalar|Param|null>,
 *             tags?: scalar|Param|null, // Default: null
 *             public?: bool|Param, // Default: false
 *             default_lifetime?: scalar|Param|null, // Default lifetime of the pool.
 *             provider?: scalar|Param|null, // Overwrite the setting from the default provider for this adapter.
 *             early_expiration_message_bus?: scalar|Param|null,
 *             clearer?: scalar|Param|null,
 *         }>,
 *     },
 *     php_errors?: array{ // PHP errors handling configuration
 *         log?: mixed, // Use the application logger instead of the PHP logger for logging PHP errors. // Default: true
 *         throw?: bool|Param, // Throw PHP errors as \ErrorException instances. // Default: true
 *     },
 *     exceptions?: array<string, array{ // Default: []
 *         log_level?: scalar|Param|null, // The level of log message. Null to let Symfony decide. // Default: null
 *         status_code?: scalar|Param|null, // The status code of the response. Null or 0 to let Symfony decide. // Default: null
 *         log_channel?: scalar|Param|null, // The channel of log message. Null to let Symfony decide. // Default: null
 *     }>,
 *     web_link?: bool|array{ // Web links configuration
 *         enabled?: bool|Param, // Default: true
 *     },
 *     lock?: bool|string|array{ // Lock configuration
 *         enabled?: bool|Param, // Default: true
 *         resources?: array<string, string|list<scalar|Param|null>>,
 *     },
 *     semaphore?: bool|string|array{ // Semaphore configuration
 *         enabled?: bool|Param, // Default: false
 *         resources?: array<string, scalar|Param|null>,
 *     },
 *     messenger?: bool|array{ // Messenger configuration
 *         enabled?: bool|Param, // Default: true
 *         routing?: array<string, array{ // Default: []
 *             senders?: list<scalar|Param|null>,
 *         }>,
 *         serializer?: array{
 *             default_serializer?: scalar|Param|null, // Service id to use as the default serializer for the transports. // Default: "messenger.transport.native_php_serializer"
 *             symfony_serializer?: array{
 *                 format?: scalar|Param|null, // Serialization format for the messenger.transport.symfony_serializer service (which is not the serializer used by default). // Default: "json"
 *                 context?: array<string, mixed>,
 *             },
 *         },
 *         transports?: array<string, string|array{ // Default: []
 *             dsn?: scalar|Param|null,
 *             serializer?: scalar|Param|null, // Service id of a custom serializer to use. // Default: null
 *             options?: list<mixed>,
 *             failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *             retry_strategy?: string|array{
 *                 service?: scalar|Param|null, // Service id to override the retry strategy entirely. // Default: null
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: this delay = (delay * (multiple ^ retries)). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness to apply to the delay (between 0 and 1). // Default: 0.1
 *             },
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use when processing messages. // Default: null
 *         }>,
 *         failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *         stop_worker_on_signals?: list<scalar|Param|null>,
 *         default_bus?: scalar|Param|null, // Default: null
 *         buses?: array<string, array{ // Default: {"messenger.bus.default":{"default_middleware":{"enabled":true,"allow_no_handlers":false,"allow_no_senders":true},"middleware":[]}}
 *             default_middleware?: bool|string|array{
 *                 enabled?: bool|Param, // Default: true
 *                 allow_no_handlers?: bool|Param, // Default: false
 *                 allow_no_senders?: bool|Param, // Default: true
 *             },
 *             middleware?: list<string|array{ // Default: []
 *                 id: scalar|Param|null,
 *                 arguments?: list<mixed>,
 *             }>,
 *         }>,
 *     },
 *     scheduler?: bool|array{ // Scheduler configuration
 *         enabled?: bool|Param, // Default: true
 *     },
 *     disallow_search_engine_index?: bool|Param, // Enabled by default when debug is enabled. // Default: true
 *     http_client?: bool|array{ // HTTP Client configuration
 *         enabled?: bool|Param, // Default: true
 *         max_host_connections?: int|Param, // The maximum number of connections to a single host.
 *         default_options?: array{
 *             headers?: array<string, mixed>,
 *             vars?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|Param|null>,
 *             proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|Param|null, // A certificate authority file.
 *             capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *             local_pk?: scalar|Param|null, // A private key file.
 *             passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...)
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                 http_codes?: array<string, array{ // Default: []
 *                     code?: int|Param,
 *                     methods?: list<string|Param>,
 *                 }>,
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *             },
 *         },
 *         mock_response_factory?: scalar|Param|null, // The id of the service that should generate mock responses. It should be either an invokable or an iterable.
 *         scoped_clients?: array<string, string|array{ // Default: []
 *             scope?: scalar|Param|null, // The regular expression that the request URL must match before adding the other options. When none is provided, the base URI is used instead.
 *             base_uri?: scalar|Param|null, // The URI to resolve relative URLs, following rules in RFC 3985, section 2.
 *             auth_basic?: scalar|Param|null, // An HTTP Basic authentication "username:password".
 *             auth_bearer?: scalar|Param|null, // A token enabling HTTP Bearer authorization.
 *             auth_ntlm?: scalar|Param|null, // A "username:password" pair to use Microsoft NTLM authentication (requires the cURL extension).
 *             query?: array<string, scalar|Param|null>,
 *             headers?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|Param|null>,
 *             proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|Param|null, // A certificate authority file.
 *             capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *             local_pk?: scalar|Param|null, // A private key file.
 *             passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...).
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                 http_codes?: array<string, array{ // Default: []
 *                     code?: int|Param,
 *                     methods?: list<string|Param>,
 *                 }>,
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *             },
 *         }>,
 *     },
 *     mailer?: bool|array{ // Mailer configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         dsn?: scalar|Param|null, // Default: null
 *         transports?: array<string, scalar|Param|null>,
 *         envelope?: array{ // Mailer Envelope configuration
 *             sender?: scalar|Param|null,
 *             recipients?: list<scalar|Param|null>,
 *             allowed_recipients?: list<scalar|Param|null>,
 *         },
 *         headers?: array<string, string|array{ // Default: []
 *             value?: mixed,
 *         }>,
 *         dkim_signer?: bool|array{ // DKIM signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Key content, or path to key (in PEM format with the `file://` prefix) // Default: ""
 *             domain?: scalar|Param|null, // Default: ""
 *             select?: scalar|Param|null, // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: ""
 *             options?: array<string, mixed>,
 *         },
 *         smime_signer?: bool|array{ // S/MIME signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Path to key (in PEM format) // Default: ""
 *             certificate?: scalar|Param|null, // Path to certificate (in PEM format without the `file://` prefix) // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: null
 *             extra_certificates?: scalar|Param|null, // Default: null
 *             sign_options?: int|Param, // Default: null
 *         },
 *         smime_encrypter?: bool|array{ // S/MIME encrypter configuration
 *             enabled?: bool|Param, // Default: false
 *             repository?: scalar|Param|null, // S/MIME certificate repository service. This service shall implement the `Symfony\Component\Mailer\EventListener\SmimeCertificateRepositoryInterface`. // Default: ""
 *             cipher?: int|Param, // A set of algorithms used to encrypt the message // Default: null
 *         },
 *     },
 *     secrets?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         vault_directory?: scalar|Param|null, // Default: "%kernel.project_dir%/config/secrets/%kernel.runtime_environment%"
 *         local_dotenv_file?: scalar|Param|null, // Default: "%kernel.project_dir%/.env.%kernel.environment%.local"
 *         decryption_env_var?: scalar|Param|null, // Default: "base64:default::SYMFONY_DECRYPTION_SECRET"
 *     },
 *     notifier?: bool|array{ // Notifier configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         chatter_transports?: array<string, scalar|Param|null>,
 *         texter_transports?: array<string, scalar|Param|null>,
 *         notification_on_failed_messages?: bool|Param, // Default: false
 *         channel_policy?: array<string, string|list<scalar|Param|null>>,
 *         admin_recipients?: list<array{ // Default: []
 *             email?: scalar|Param|null,
 *             phone?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     rate_limiter?: bool|array{ // Rate limiter configuration
 *         enabled?: bool|Param, // Default: true
 *         limiters?: array<string, array{ // Default: []
 *             lock_factory?: scalar|Param|null, // The service ID of the lock factory used by this limiter (or null to disable locking). // Default: "auto"
 *             cache_pool?: scalar|Param|null, // The cache pool to use for storing the current limiter state. // Default: "cache.rate_limiter"
 *             storage_service?: scalar|Param|null, // The service ID of a custom storage implementation, this precedes any configured "cache_pool". // Default: null
 *             policy: "fixed_window"|"token_bucket"|"sliding_window"|"compound"|"no_limit"|Param, // The algorithm to be used by this limiter.
 *             limiters?: list<scalar|Param|null>,
 *             limit?: int|Param, // The maximum allowed hits in a fixed interval or burst.
 *             interval?: scalar|Param|null, // Configures the fixed interval if "policy" is set to "fixed_window" or "sliding_window". The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *             rate?: array{ // Configures the fill rate if "policy" is set to "token_bucket".
 *                 interval?: scalar|Param|null, // Configures the rate interval. The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *                 amount?: int|Param, // Amount of tokens to add each interval. // Default: 1
 *             },
 *         }>,
 *     },
 *     uid?: bool|array{ // Uid configuration
 *         enabled?: bool|Param, // Default: true
 *         default_uuid_version?: 7|6|4|1|Param, // Default: 7
 *         name_based_uuid_version?: 5|3|Param, // Default: 5
 *         name_based_uuid_namespace?: scalar|Param|null,
 *         time_based_uuid_version?: 7|6|1|Param, // Default: 7
 *         time_based_uuid_node?: scalar|Param|null,
 *     },
 *     html_sanitizer?: bool|array{ // HtmlSanitizer configuration
 *         enabled?: bool|Param, // Default: true
 *         sanitizers?: array<string, array{ // Default: []
 *             allow_safe_elements?: bool|Param, // Allows "safe" elements and attributes. // Default: false
 *             allow_static_elements?: bool|Param, // Allows all static elements and attributes from the W3C Sanitizer API standard. // Default: false
 *             allow_elements?: array<string, mixed>,
 *             block_elements?: list<string|Param>,
 *             drop_elements?: list<string|Param>,
 *             allow_attributes?: array<string, mixed>,
 *             drop_attributes?: array<string, mixed>,
 *             force_attributes?: array<string, array<string, string|Param>>,
 *             force_https_urls?: bool|Param, // Transforms URLs using the HTTP scheme to use the HTTPS scheme instead. // Default: false
 *             allowed_link_schemes?: list<string|Param>,
 *             allowed_link_hosts?: list<string|Param>|null,
 *             allow_relative_links?: bool|Param, // Allows relative URLs to be used in links href attributes. // Default: false
 *             allowed_media_schemes?: list<string|Param>,
 *             allowed_media_hosts?: list<string|Param>|null,
 *             allow_relative_medias?: bool|Param, // Allows relative URLs to be used in media source attributes (img, audio, video, ...). // Default: false
 *             with_attribute_sanitizers?: list<string|Param>,
 *             without_attribute_sanitizers?: list<string|Param>,
 *             max_input_length?: int|Param, // The maximum length allowed for the sanitized input. // Default: 0
 *         }>,
 *     },
 *     webhook?: bool|array{ // Webhook configuration
 *         enabled?: bool|Param, // Default: false
 *         message_bus?: scalar|Param|null, // The message bus to use. // Default: "messenger.default_bus"
 *         routing?: array<string, array{ // Default: []
 *             service: scalar|Param|null,
 *             secret?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     remote-event?: bool|array{ // RemoteEvent configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     json_streamer?: bool|array{ // JSON streamer configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 * }
 * @psalm-type SecurityConfig = array{
 *     access_denied_url?: scalar|Param|null, // Default: null
 *     session_fixation_strategy?: "none"|"migrate"|"invalidate"|Param, // Default: "migrate"
 *     hide_user_not_found?: bool|Param, // Deprecated: The "hide_user_not_found" option is deprecated and will be removed in 8.0. Use the "expose_security_errors" option instead.
 *     expose_security_errors?: \Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::None|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::AccountStatus|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::All|Param, // Default: "none"
 *     erase_credentials?: bool|Param, // Default: true
 *     access_decision_manager?: array{
 *         strategy?: "affirmative"|"consensus"|"unanimous"|"priority"|Param,
 *         service?: scalar|Param|null,
 *         strategy_service?: scalar|Param|null,
 *         allow_if_all_abstain?: bool|Param, // Default: false
 *         allow_if_equal_granted_denied?: bool|Param, // Default: true
 *     },
 *     password_hashers?: array<string, string|array{ // Default: []
 *         algorithm?: scalar|Param|null,
 *         migrate_from?: list<scalar|Param|null>,
 *         hash_algorithm?: scalar|Param|null, // Name of hashing algorithm for PBKDF2 (i.e. sha256, sha512, etc..) See hash_algos() for a list of supported algorithms. // Default: "sha512"
 *         key_length?: scalar|Param|null, // Default: 40
 *         ignore_case?: bool|Param, // Default: false
 *         encode_as_base64?: bool|Param, // Default: true
 *         iterations?: scalar|Param|null, // Default: 5000
 *         cost?: int|Param, // Default: null
 *         memory_cost?: scalar|Param|null, // Default: null
 *         time_cost?: scalar|Param|null, // Default: null
 *         id?: scalar|Param|null,
 *     }>,
 *     providers?: array<string, array{ // Default: []
 *         id?: scalar|Param|null,
 *         chain?: array{
 *             providers?: list<scalar|Param|null>,
 *         },
 *         memory?: array{
 *             users?: array<string, array{ // Default: []
 *                 password?: scalar|Param|null, // Default: null
 *                 roles?: list<scalar|Param|null>,
 *             }>,
 *         },
 *         ldap?: array{
 *             service: scalar|Param|null,
 *             base_dn: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: null
 *             search_password?: scalar|Param|null, // Default: null
 *             extra_fields?: list<scalar|Param|null>,
 *             default_roles?: list<scalar|Param|null>,
 *             role_fetcher?: scalar|Param|null, // Default: null
 *             uid_key?: scalar|Param|null, // Default: "sAMAccountName"
 *             filter?: scalar|Param|null, // Default: "({uid_key}={user_identifier})"
 *             password_attribute?: scalar|Param|null, // Default: null
 *         },
 *         entity?: array{
 *             class: scalar|Param|null, // The full entity class name of your user class.
 *             property?: scalar|Param|null, // Default: null
 *             manager_name?: scalar|Param|null, // Default: null
 *         },
 *     }>,
 *     firewalls: array<string, array{ // Default: []
 *         pattern?: scalar|Param|null,
 *         host?: scalar|Param|null,
 *         methods?: list<scalar|Param|null>,
 *         security?: bool|Param, // Default: true
 *         user_checker?: scalar|Param|null, // The UserChecker to use when authenticating users in this firewall. // Default: "security.user_checker"
 *         request_matcher?: scalar|Param|null,
 *         access_denied_url?: scalar|Param|null,
 *         access_denied_handler?: scalar|Param|null,
 *         entry_point?: scalar|Param|null, // An enabled authenticator name or a service id that implements "Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface".
 *         provider?: scalar|Param|null,
 *         stateless?: bool|Param, // Default: false
 *         lazy?: bool|Param, // Default: false
 *         context?: scalar|Param|null,
 *         logout?: array{
 *             enable_csrf?: bool|Param|null, // Default: null
 *             csrf_token_id?: scalar|Param|null, // Default: "logout"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_manager?: scalar|Param|null,
 *             path?: scalar|Param|null, // Default: "/logout"
 *             target?: scalar|Param|null, // Default: "/"
 *             invalidate_session?: bool|Param, // Default: true
 *             clear_site_data?: list<"*"|"cache"|"cookies"|"storage"|"executionContexts"|Param>,
 *             delete_cookies?: array<string, array{ // Default: []
 *                 path?: scalar|Param|null, // Default: null
 *                 domain?: scalar|Param|null, // Default: null
 *                 secure?: scalar|Param|null, // Default: false
 *                 samesite?: scalar|Param|null, // Default: null
 *                 partitioned?: scalar|Param|null, // Default: false
 *             }>,
 *         },
 *         switch_user?: array{
 *             provider?: scalar|Param|null,
 *             parameter?: scalar|Param|null, // Default: "_switch_user"
 *             role?: scalar|Param|null, // Default: "ROLE_ALLOWED_TO_SWITCH"
 *             target_route?: scalar|Param|null, // Default: null
 *         },
 *         required_badges?: list<scalar|Param|null>,
 *         custom_authenticators?: list<scalar|Param|null>,
 *         login_throttling?: array{
 *             limiter?: scalar|Param|null, // A service id implementing "Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface".
 *             max_attempts?: int|Param, // Default: 5
 *             interval?: scalar|Param|null, // Default: "1 minute"
 *             lock_factory?: scalar|Param|null, // The service ID of the lock factory used by the login rate limiter (or null to disable locking). // Default: null
 *             cache_pool?: string|Param, // The cache pool to use for storing the limiter state // Default: "cache.rate_limiter"
 *             storage_service?: string|Param, // The service ID of a custom storage implementation, this precedes any configured "cache_pool" // Default: null
 *         },
 *         two_factor?: array{
 *             check_path?: scalar|Param|null, // Default: "/2fa_check"
 *             post_only?: bool|Param, // Default: true
 *             auth_form_path?: scalar|Param|null, // Default: "/2fa"
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             success_handler?: scalar|Param|null, // Default: null
 *             failure_handler?: scalar|Param|null, // Default: null
 *             authentication_required_handler?: scalar|Param|null, // Default: null
 *             auth_code_parameter_name?: scalar|Param|null, // Default: "_auth_code"
 *             trusted_parameter_name?: scalar|Param|null, // Default: "_trusted"
 *             remember_me_sets_trusted?: scalar|Param|null, // Default: false
 *             multi_factor?: bool|Param, // Default: false
 *             prepare_on_login?: bool|Param, // Default: false
 *             prepare_on_access_denied?: bool|Param, // Default: false
 *             enable_csrf?: scalar|Param|null, // Default: false
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|Param|null, // Default: "two_factor"
 *             csrf_header?: scalar|Param|null, // Default: null
 *             csrf_token_manager?: scalar|Param|null, // Default: "scheb_two_factor.csrf_token_manager"
 *             provider?: scalar|Param|null, // Default: null
 *         },
 *         x509?: array{
 *             provider?: scalar|Param|null,
 *             user?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN_Email"
 *             credentials?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN"
 *             user_identifier?: scalar|Param|null, // Default: "emailAddress"
 *         },
 *         remote_user?: array{
 *             provider?: scalar|Param|null,
 *             user?: scalar|Param|null, // Default: "REMOTE_USER"
 *         },
 *         login_link?: array{
 *             check_route: scalar|Param|null, // Route that will validate the login link - e.g. "app_login_link_verify".
 *             check_post_only?: scalar|Param|null, // If true, only HTTP POST requests to "check_route" will be handled by the authenticator. // Default: false
 *             signature_properties: list<scalar|Param|null>,
 *             lifetime?: int|Param, // The lifetime of the login link in seconds. // Default: 600
 *             max_uses?: int|Param, // Max number of times a login link can be used - null means unlimited within lifetime. // Default: null
 *             used_link_cache?: scalar|Param|null, // Cache service id used to expired links of max_uses is set.
 *             success_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface.
 *             failure_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface.
 *             provider?: scalar|Param|null, // The user provider to load users from.
 *             secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *         },
 *         form_login?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_parameter?: scalar|Param|null, // Default: "_username"
 *             password_parameter?: scalar|Param|null, // Default: "_password"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *         },
 *         form_login_ldap?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_parameter?: scalar|Param|null, // Default: "_username"
 *             password_parameter?: scalar|Param|null, // Default: "_password"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         json_login?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_path?: scalar|Param|null, // Default: "username"
 *             password_path?: scalar|Param|null, // Default: "password"
 *         },
 *         json_login_ldap?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_path?: scalar|Param|null, // Default: "username"
 *             password_path?: scalar|Param|null, // Default: "password"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         access_token?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: null
 *             token_extractors?: list<scalar|Param|null>,
 *             token_handler: string|array{
 *                 id?: scalar|Param|null,
 *                 oidc_user_info?: string|array{
 *                     base_uri: scalar|Param|null, // Base URI of the userinfo endpoint on the OIDC server, or the OIDC server URI to use the discovery (require "discovery" to be configured).
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         cache?: array{
 *                             id: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                     },
 *                     claim?: scalar|Param|null, // Claim which contains the user identifier (e.g. sub, email, etc.). // Default: "sub"
 *                     client?: scalar|Param|null, // HttpClient service id to use to call the OIDC server.
 *                 },
 *                 oidc?: array{
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         base_uri: list<scalar|Param|null>,
 *                         cache?: array{
 *                             id: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                     },
 *                     claim?: scalar|Param|null, // Claim which contains the user identifier (e.g.: sub, email..). // Default: "sub"
 *                     audience: scalar|Param|null, // Audience set in the token, for validation purpose.
 *                     issuers: list<scalar|Param|null>,
 *                     algorithm?: array<mixed>,
 *                     algorithms: list<scalar|Param|null>,
 *                     key?: scalar|Param|null, // Deprecated: The "key" option is deprecated and will be removed in 8.0. Use the "keyset" option instead. // JSON-encoded JWK used to sign the token (must contain a "kty" key).
 *                     keyset?: scalar|Param|null, // JSON-encoded JWKSet used to sign the token (must contain a list of valid public keys).
 *                     encryption?: bool|array{
 *                         enabled?: bool|Param, // Default: false
 *                         enforce?: bool|Param, // When enabled, the token shall be encrypted. // Default: false
 *                         algorithms: list<scalar|Param|null>,
 *                         keyset: scalar|Param|null, // JSON-encoded JWKSet used to decrypt the token (must contain a list of valid private keys).
 *                     },
 *                 },
 *                 cas?: array{
 *                     validation_url: scalar|Param|null, // CAS server validation URL
 *                     prefix?: scalar|Param|null, // CAS prefix // Default: "cas"
 *                     http_client?: scalar|Param|null, // HTTP Client service // Default: null
 *                 },
 *                 oauth2?: scalar|Param|null,
 *             },
 *         },
 *         http_basic?: array{
 *             provider?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: "Secured Area"
 *         },
 *         http_basic_ldap?: array{
 *             provider?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: "Secured Area"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         remember_me?: array{
 *             secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *             service?: scalar|Param|null,
 *             user_providers?: list<scalar|Param|null>,
 *             catch_exceptions?: bool|Param, // Default: true
 *             signature_properties?: list<scalar|Param|null>,
 *             token_provider?: string|array{
 *                 service?: scalar|Param|null, // The service ID of a custom remember-me token provider.
 *                 doctrine?: bool|array{
 *                     enabled?: bool|Param, // Default: false
 *                     connection?: scalar|Param|null, // Default: null
 *                 },
 *             },
 *             token_verifier?: scalar|Param|null, // The service ID of a custom rememberme token verifier.
 *             name?: scalar|Param|null, // Default: "REMEMBERME"
 *             lifetime?: int|Param, // Default: 31536000
 *             path?: scalar|Param|null, // Default: "/"
 *             domain?: scalar|Param|null, // Default: null
 *             secure?: true|false|"auto"|Param, // Default: null
 *             httponly?: bool|Param, // Default: true
 *             samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *             always_remember_me?: bool|Param, // Default: false
 *             remember_me_parameter?: scalar|Param|null, // Default: "_remember_me"
 *         },
 *     }>,
 *     access_control?: list<array{ // Default: []
 *         request_matcher?: scalar|Param|null, // Default: null
 *         requires_channel?: scalar|Param|null, // Default: null
 *         path?: scalar|Param|null, // Use the urldecoded format. // Default: null
 *         host?: scalar|Param|null, // Default: null
 *         port?: int|Param, // Default: null
 *         ips?: list<scalar|Param|null>,
 *         attributes?: array<string, scalar|Param|null>,
 *         route?: scalar|Param|null, // Default: null
 *         methods?: list<scalar|Param|null>,
 *         allow_if?: scalar|Param|null, // Default: null
 *         roles?: list<scalar|Param|null>,
 *     }>,
 *     role_hierarchy?: array<string, string|list<scalar|Param|null>>,
 * }
 * @psalm-type TwigConfig = array{
 *     form_themes?: list<scalar|Param|null>,
 *     globals?: array<string, array{ // Default: []
 *         id?: scalar|Param|null,
 *         type?: scalar|Param|null,
 *         value?: mixed,
 *     }>,
 *     autoescape_service?: scalar|Param|null, // Default: null
 *     autoescape_service_method?: scalar|Param|null, // Default: null
 *     base_template_class?: scalar|Param|null, // Deprecated: The child node "base_template_class" at path "twig.base_template_class" is deprecated.
 *     cache?: scalar|Param|null, // Default: true
 *     charset?: scalar|Param|null, // Default: "%kernel.charset%"
 *     debug?: bool|Param, // Default: "%kernel.debug%"
 *     strict_variables?: bool|Param, // Default: "%kernel.debug%"
 *     auto_reload?: scalar|Param|null,
 *     optimizations?: int|Param,
 *     default_path?: scalar|Param|null, // The default path used to load templates. // Default: "%kernel.project_dir%/templates"
 *     file_name_pattern?: list<scalar|Param|null>,
 *     paths?: array<string, mixed>,
 *     date?: array{ // The default format options used by the date filter.
 *         format?: scalar|Param|null, // Default: "F j, Y H:i"
 *         interval_format?: scalar|Param|null, // Default: "%d days"
 *         timezone?: scalar|Param|null, // The timezone used when formatting dates, when set to null, the timezone returned by date_default_timezone_get() is used. // Default: null
 *     },
 *     number_format?: array{ // The default format options for the number_format filter.
 *         decimals?: int|Param, // Default: 0
 *         decimal_point?: scalar|Param|null, // Default: "."
 *         thousands_separator?: scalar|Param|null, // Default: ","
 *     },
 *     mailer?: array{
 *         html_to_text_converter?: scalar|Param|null, // A service implementing the "Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface". // Default: null
 *     },
 * }
 * @psalm-type TwigExtraConfig = array{
 *     cache?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     html?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     markdown?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     intl?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     cssinliner?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     inky?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     string?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     commonmark?: array{
 *         renderer?: array{ // Array of options for rendering HTML.
 *             block_separator?: scalar|Param|null,
 *             inner_separator?: scalar|Param|null,
 *             soft_break?: scalar|Param|null,
 *         },
 *         html_input?: "strip"|"allow"|"escape"|Param, // How to handle HTML input.
 *         allow_unsafe_links?: bool|Param, // Remove risky link and image URLs by setting this to false. // Default: true
 *         max_nesting_level?: int|Param, // The maximum nesting level for blocks. // Default: 9223372036854775807
 *         max_delimiters_per_line?: int|Param, // The maximum number of strong/emphasis delimiters per line. // Default: 9223372036854775807
 *         slug_normalizer?: array{ // Array of options for configuring how URL-safe slugs are created.
 *             instance?: mixed,
 *             max_length?: int|Param, // Default: 255
 *             unique?: mixed,
 *         },
 *         commonmark?: array{ // Array of options for configuring the CommonMark core extension.
 *             enable_em?: bool|Param, // Default: true
 *             enable_strong?: bool|Param, // Default: true
 *             use_asterisk?: bool|Param, // Default: true
 *             use_underscore?: bool|Param, // Default: true
 *             unordered_list_markers?: list<scalar|Param|null>,
 *         },
 *         ...<mixed>
 *     },
 * }
 * @psalm-type MonologConfig = array{
 *     use_microseconds?: scalar|Param|null, // Default: true
 *     channels?: list<scalar|Param|null>,
 *     handlers?: array<string, array{ // Default: []
 *         type: scalar|Param|null,
 *         id?: scalar|Param|null,
 *         enabled?: bool|Param, // Default: true
 *         priority?: scalar|Param|null, // Default: 0
 *         level?: scalar|Param|null, // Default: "DEBUG"
 *         bubble?: bool|Param, // Default: true
 *         interactive_only?: bool|Param, // Default: false
 *         app_name?: scalar|Param|null, // Default: null
 *         fill_extra_context?: bool|Param, // Default: false
 *         include_stacktraces?: bool|Param, // Default: false
 *         process_psr_3_messages?: array{
 *             enabled?: bool|Param|null, // Default: null
 *             date_format?: scalar|Param|null,
 *             remove_used_context_fields?: bool|Param,
 *         },
 *         path?: scalar|Param|null, // Default: "%kernel.logs_dir%/%kernel.environment%.log"
 *         file_permission?: scalar|Param|null, // Default: null
 *         use_locking?: bool|Param, // Default: false
 *         filename_format?: scalar|Param|null, // Default: "{filename}-{date}"
 *         date_format?: scalar|Param|null, // Default: "Y-m-d"
 *         ident?: scalar|Param|null, // Default: false
 *         logopts?: scalar|Param|null, // Default: 1
 *         facility?: scalar|Param|null, // Default: "user"
 *         max_files?: scalar|Param|null, // Default: 0
 *         action_level?: scalar|Param|null, // Default: "WARNING"
 *         activation_strategy?: scalar|Param|null, // Default: null
 *         stop_buffering?: bool|Param, // Default: true
 *         passthru_level?: scalar|Param|null, // Default: null
 *         excluded_404s?: list<scalar|Param|null>,
 *         excluded_http_codes?: list<array{ // Default: []
 *             code?: scalar|Param|null,
 *             urls?: list<scalar|Param|null>,
 *         }>,
 *         accepted_levels?: list<scalar|Param|null>,
 *         min_level?: scalar|Param|null, // Default: "DEBUG"
 *         max_level?: scalar|Param|null, // Default: "EMERGENCY"
 *         buffer_size?: scalar|Param|null, // Default: 0
 *         flush_on_overflow?: bool|Param, // Default: false
 *         handler?: scalar|Param|null,
 *         url?: scalar|Param|null,
 *         exchange?: scalar|Param|null,
 *         exchange_name?: scalar|Param|null, // Default: "log"
 *         room?: scalar|Param|null,
 *         message_format?: scalar|Param|null, // Default: "text"
 *         api_version?: scalar|Param|null, // Default: null
 *         channel?: scalar|Param|null, // Default: null
 *         bot_name?: scalar|Param|null, // Default: "Monolog"
 *         use_attachment?: scalar|Param|null, // Default: true
 *         use_short_attachment?: scalar|Param|null, // Default: false
 *         include_extra?: scalar|Param|null, // Default: false
 *         icon_emoji?: scalar|Param|null, // Default: null
 *         webhook_url?: scalar|Param|null,
 *         exclude_fields?: list<scalar|Param|null>,
 *         team?: scalar|Param|null,
 *         notify?: scalar|Param|null, // Default: false
 *         nickname?: scalar|Param|null, // Default: "Monolog"
 *         token?: scalar|Param|null,
 *         region?: scalar|Param|null,
 *         source?: scalar|Param|null,
 *         use_ssl?: bool|Param, // Default: true
 *         user?: mixed,
 *         title?: scalar|Param|null, // Default: null
 *         host?: scalar|Param|null, // Default: null
 *         port?: scalar|Param|null, // Default: 514
 *         config?: list<scalar|Param|null>,
 *         members?: list<scalar|Param|null>,
 *         connection_string?: scalar|Param|null,
 *         timeout?: scalar|Param|null,
 *         time?: scalar|Param|null, // Default: 60
 *         deduplication_level?: scalar|Param|null, // Default: 400
 *         store?: scalar|Param|null, // Default: null
 *         connection_timeout?: scalar|Param|null,
 *         persistent?: bool|Param,
 *         dsn?: scalar|Param|null,
 *         hub_id?: scalar|Param|null, // Default: null
 *         client_id?: scalar|Param|null, // Default: null
 *         auto_log_stacks?: scalar|Param|null, // Default: false
 *         release?: scalar|Param|null, // Default: null
 *         environment?: scalar|Param|null, // Default: null
 *         message_type?: scalar|Param|null, // Default: 0
 *         parse_mode?: scalar|Param|null, // Default: null
 *         disable_webpage_preview?: bool|Param|null, // Default: null
 *         disable_notification?: bool|Param|null, // Default: null
 *         split_long_messages?: bool|Param, // Default: false
 *         delay_between_messages?: bool|Param, // Default: false
 *         topic?: int|Param, // Default: null
 *         factor?: int|Param, // Default: 1
 *         tags?: list<scalar|Param|null>,
 *         console_formater_options?: mixed, // Deprecated: "monolog.handlers..console_formater_options.console_formater_options" is deprecated, use "monolog.handlers..console_formater_options.console_formatter_options" instead.
 *         console_formatter_options?: mixed, // Default: []
 *         formatter?: scalar|Param|null,
 *         nested?: bool|Param, // Default: false
 *         publisher?: string|array{
 *             id?: scalar|Param|null,
 *             hostname?: scalar|Param|null,
 *             port?: scalar|Param|null, // Default: 12201
 *             chunk_size?: scalar|Param|null, // Default: 1420
 *             encoder?: "json"|"compressed_json"|Param,
 *         },
 *         mongo?: string|array{
 *             id?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *             port?: scalar|Param|null, // Default: 27017
 *             user?: scalar|Param|null,
 *             pass?: scalar|Param|null,
 *             database?: scalar|Param|null, // Default: "monolog"
 *             collection?: scalar|Param|null, // Default: "logs"
 *         },
 *         mongodb?: string|array{
 *             id?: scalar|Param|null, // ID of a MongoDB\Client service
 *             uri?: scalar|Param|null,
 *             username?: scalar|Param|null,
 *             password?: scalar|Param|null,
 *             database?: scalar|Param|null, // Default: "monolog"
 *             collection?: scalar|Param|null, // Default: "logs"
 *         },
 *         elasticsearch?: string|array{
 *             id?: scalar|Param|null,
 *             hosts?: list<scalar|Param|null>,
 *             host?: scalar|Param|null,
 *             port?: scalar|Param|null, // Default: 9200
 *             transport?: scalar|Param|null, // Default: "Http"
 *             user?: scalar|Param|null, // Default: null
 *             password?: scalar|Param|null, // Default: null
 *         },
 *         index?: scalar|Param|null, // Default: "monolog"
 *         document_type?: scalar|Param|null, // Default: "logs"
 *         ignore_error?: scalar|Param|null, // Default: false
 *         redis?: string|array{
 *             id?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *             password?: scalar|Param|null, // Default: null
 *             port?: scalar|Param|null, // Default: 6379
 *             database?: scalar|Param|null, // Default: 0
 *             key_name?: scalar|Param|null, // Default: "monolog_redis"
 *         },
 *         predis?: string|array{
 *             id?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *         },
 *         from_email?: scalar|Param|null,
 *         to_email?: list<scalar|Param|null>,
 *         subject?: scalar|Param|null,
 *         content_type?: scalar|Param|null, // Default: null
 *         headers?: list<scalar|Param|null>,
 *         mailer?: scalar|Param|null, // Default: null
 *         email_prototype?: string|array{
 *             id: scalar|Param|null,
 *             method?: scalar|Param|null, // Default: null
 *         },
 *         lazy?: bool|Param, // Default: true
 *         verbosity_levels?: array{
 *             VERBOSITY_QUIET?: scalar|Param|null, // Default: "ERROR"
 *             VERBOSITY_NORMAL?: scalar|Param|null, // Default: "WARNING"
 *             VERBOSITY_VERBOSE?: scalar|Param|null, // Default: "NOTICE"
 *             VERBOSITY_VERY_VERBOSE?: scalar|Param|null, // Default: "INFO"
 *             VERBOSITY_DEBUG?: scalar|Param|null, // Default: "DEBUG"
 *         },
 *         channels?: string|array{
 *             type?: scalar|Param|null,
 *             elements?: list<scalar|Param|null>,
 *         },
 *     }>,
 * }
 * @psalm-type DoctrineConfig = array{
 *     dbal?: array{
 *         default_connection?: scalar|Param|null,
 *         types?: array<string, string|array{ // Default: []
 *             class: scalar|Param|null,
 *             commented?: bool|Param, // Deprecated: The doctrine-bundle type commenting features were removed; the corresponding config parameter was deprecated in 2.0 and will be dropped in 3.0.
 *         }>,
 *         driver_schemes?: array<string, scalar|Param|null>,
 *         connections?: array<string, array{ // Default: []
 *             url?: scalar|Param|null, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *             dbname?: scalar|Param|null,
 *             host?: scalar|Param|null, // Defaults to "localhost" at runtime.
 *             port?: scalar|Param|null, // Defaults to null at runtime.
 *             user?: scalar|Param|null, // Defaults to "root" at runtime.
 *             password?: scalar|Param|null, // Defaults to null at runtime.
 *             override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *             dbname_suffix?: scalar|Param|null, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *             application_name?: scalar|Param|null,
 *             charset?: scalar|Param|null,
 *             path?: scalar|Param|null,
 *             memory?: bool|Param,
 *             unix_socket?: scalar|Param|null, // The unix socket to use for MySQL
 *             persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *             protocol?: scalar|Param|null, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *             service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *             servicename?: scalar|Param|null, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *             sessionMode?: scalar|Param|null, // The session mode to use for the oci8 driver
 *             server?: scalar|Param|null, // The name of a running database server to connect to for SQL Anywhere.
 *             default_dbname?: scalar|Param|null, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *             sslmode?: scalar|Param|null, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *             sslrootcert?: scalar|Param|null, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *             sslcert?: scalar|Param|null, // The path to the SSL client certificate file for PostgreSQL.
 *             sslkey?: scalar|Param|null, // The path to the SSL client key file for PostgreSQL.
 *             sslcrl?: scalar|Param|null, // The file name of the SSL certificate revocation list for PostgreSQL.
 *             pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *             MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *             use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *             instancename?: scalar|Param|null, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *             connectstring?: scalar|Param|null, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             driver?: scalar|Param|null, // Default: "pdo_mysql"
 *             platform_service?: scalar|Param|null, // Deprecated: The "platform_service" configuration key is deprecated since doctrine-bundle 2.9. DBAL 4 will not support setting a custom platform via connection params anymore.
 *             auto_commit?: bool|Param,
 *             schema_filter?: scalar|Param|null,
 *             logging?: bool|Param, // Default: true
 *             profiling?: bool|Param, // Default: true
 *             profiling_collect_backtrace?: bool|Param, // Enables collecting backtraces when profiling is enabled // Default: false
 *             profiling_collect_schema_errors?: bool|Param, // Enables collecting schema errors when profiling is enabled // Default: true
 *             disable_type_comments?: bool|Param,
 *             server_version?: scalar|Param|null,
 *             idle_connection_ttl?: int|Param, // Default: 600
 *             driver_class?: scalar|Param|null,
 *             wrapper_class?: scalar|Param|null,
 *             keep_slave?: bool|Param, // Deprecated: The "keep_slave" configuration key is deprecated since doctrine-bundle 2.2. Use the "keep_replica" configuration key instead.
 *             keep_replica?: bool|Param,
 *             options?: array<string, mixed>,
 *             mapping_types?: array<string, scalar|Param|null>,
 *             default_table_options?: array<string, scalar|Param|null>,
 *             schema_manager_factory?: scalar|Param|null, // Default: "doctrine.dbal.default_schema_manager_factory"
 *             result_cache?: scalar|Param|null,
 *             slaves?: array<string, array{ // Default: []
 *                 url?: scalar|Param|null, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *                 dbname?: scalar|Param|null,
 *                 host?: scalar|Param|null, // Defaults to "localhost" at runtime.
 *                 port?: scalar|Param|null, // Defaults to null at runtime.
 *                 user?: scalar|Param|null, // Defaults to "root" at runtime.
 *                 password?: scalar|Param|null, // Defaults to null at runtime.
 *                 override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *                 dbname_suffix?: scalar|Param|null, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *                 application_name?: scalar|Param|null,
 *                 charset?: scalar|Param|null,
 *                 path?: scalar|Param|null,
 *                 memory?: bool|Param,
 *                 unix_socket?: scalar|Param|null, // The unix socket to use for MySQL
 *                 persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *                 protocol?: scalar|Param|null, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *                 service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *                 servicename?: scalar|Param|null, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *                 sessionMode?: scalar|Param|null, // The session mode to use for the oci8 driver
 *                 server?: scalar|Param|null, // The name of a running database server to connect to for SQL Anywhere.
 *                 default_dbname?: scalar|Param|null, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *                 sslmode?: scalar|Param|null, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *                 sslrootcert?: scalar|Param|null, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *                 sslcert?: scalar|Param|null, // The path to the SSL client certificate file for PostgreSQL.
 *                 sslkey?: scalar|Param|null, // The path to the SSL client key file for PostgreSQL.
 *                 sslcrl?: scalar|Param|null, // The file name of the SSL certificate revocation list for PostgreSQL.
 *                 pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *                 MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *                 use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *                 instancename?: scalar|Param|null, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *                 connectstring?: scalar|Param|null, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             }>,
 *             replicas?: array<string, array{ // Default: []
 *                 url?: scalar|Param|null, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *                 dbname?: scalar|Param|null,
 *                 host?: scalar|Param|null, // Defaults to "localhost" at runtime.
 *                 port?: scalar|Param|null, // Defaults to null at runtime.
 *                 user?: scalar|Param|null, // Defaults to "root" at runtime.
 *                 password?: scalar|Param|null, // Defaults to null at runtime.
 *                 override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *                 dbname_suffix?: scalar|Param|null, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *                 application_name?: scalar|Param|null,
 *                 charset?: scalar|Param|null,
 *                 path?: scalar|Param|null,
 *                 memory?: bool|Param,
 *                 unix_socket?: scalar|Param|null, // The unix socket to use for MySQL
 *                 persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *                 protocol?: scalar|Param|null, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *                 service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *                 servicename?: scalar|Param|null, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *                 sessionMode?: scalar|Param|null, // The session mode to use for the oci8 driver
 *                 server?: scalar|Param|null, // The name of a running database server to connect to for SQL Anywhere.
 *                 default_dbname?: scalar|Param|null, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *                 sslmode?: scalar|Param|null, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *                 sslrootcert?: scalar|Param|null, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *                 sslcert?: scalar|Param|null, // The path to the SSL client certificate file for PostgreSQL.
 *                 sslkey?: scalar|Param|null, // The path to the SSL client key file for PostgreSQL.
 *                 sslcrl?: scalar|Param|null, // The file name of the SSL certificate revocation list for PostgreSQL.
 *                 pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *                 MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *                 use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *                 instancename?: scalar|Param|null, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *                 connectstring?: scalar|Param|null, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             }>,
 *         }>,
 *     },
 *     orm?: array{
 *         default_entity_manager?: scalar|Param|null,
 *         auto_generate_proxy_classes?: scalar|Param|null, // Auto generate mode possible values are: "NEVER", "ALWAYS", "FILE_NOT_EXISTS", "EVAL", "FILE_NOT_EXISTS_OR_CHANGED", this option is ignored when the "enable_native_lazy_objects" option is true // Default: false
 *         enable_lazy_ghost_objects?: bool|Param, // Enables the new implementation of proxies based on lazy ghosts instead of using the legacy implementation // Default: true
 *         enable_native_lazy_objects?: bool|Param, // Enables the new native implementation of PHP lazy objects instead of generated proxies // Default: false
 *         proxy_dir?: scalar|Param|null, // Configures the path where generated proxy classes are saved when using non-native lazy objects, this option is ignored when the "enable_native_lazy_objects" option is true // Default: "%kernel.build_dir%/doctrine/orm/Proxies"
 *         proxy_namespace?: scalar|Param|null, // Defines the root namespace for generated proxy classes when using non-native lazy objects, this option is ignored when the "enable_native_lazy_objects" option is true // Default: "Proxies"
 *         controller_resolver?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             auto_mapping?: bool|Param|null, // Set to false to disable using route placeholders as lookup criteria when the primary key doesn't match the argument name // Default: null
 *             evict_cache?: bool|Param, // Set to true to fetch the entity from the database instead of using the cache, if any // Default: false
 *         },
 *         entity_managers?: array<string, array{ // Default: []
 *             query_cache_driver?: string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             metadata_cache_driver?: string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             result_cache_driver?: string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             entity_listeners?: array{
 *                 entities?: array<string, array{ // Default: []
 *                     listeners?: array<string, array{ // Default: []
 *                         events?: list<array{ // Default: []
 *                             type?: scalar|Param|null,
 *                             method?: scalar|Param|null, // Default: null
 *                         }>,
 *                     }>,
 *                 }>,
 *             },
 *             connection?: scalar|Param|null,
 *             class_metadata_factory_name?: scalar|Param|null, // Default: "Doctrine\\ORM\\Mapping\\ClassMetadataFactory"
 *             default_repository_class?: scalar|Param|null, // Default: "Doctrine\\ORM\\EntityRepository"
 *             auto_mapping?: scalar|Param|null, // Default: false
 *             naming_strategy?: scalar|Param|null, // Default: "doctrine.orm.naming_strategy.default"
 *             quote_strategy?: scalar|Param|null, // Default: "doctrine.orm.quote_strategy.default"
 *             typed_field_mapper?: scalar|Param|null, // Default: "doctrine.orm.typed_field_mapper.default"
 *             entity_listener_resolver?: scalar|Param|null, // Default: null
 *             fetch_mode_subselect_batch_size?: scalar|Param|null,
 *             repository_factory?: scalar|Param|null, // Default: "doctrine.orm.container_repository_factory"
 *             schema_ignore_classes?: list<scalar|Param|null>,
 *             report_fields_where_declared?: bool|Param, // Set to "true" to opt-in to the new mapping driver mode that was added in Doctrine ORM 2.16 and will be mandatory in ORM 3.0. See https://github.com/doctrine/orm/pull/10455. // Default: true
 *             validate_xml_mapping?: bool|Param, // Set to "true" to opt-in to the new mapping driver mode that was added in Doctrine ORM 2.14. See https://github.com/doctrine/orm/pull/6728. // Default: false
 *             second_level_cache?: array{
 *                 region_cache_driver?: string|array{
 *                     type?: scalar|Param|null, // Default: null
 *                     id?: scalar|Param|null,
 *                     pool?: scalar|Param|null,
 *                 },
 *                 region_lock_lifetime?: scalar|Param|null, // Default: 60
 *                 log_enabled?: bool|Param, // Default: true
 *                 region_lifetime?: scalar|Param|null, // Default: 3600
 *                 enabled?: bool|Param, // Default: true
 *                 factory?: scalar|Param|null,
 *                 regions?: array<string, array{ // Default: []
 *                     cache_driver?: string|array{
 *                         type?: scalar|Param|null, // Default: null
 *                         id?: scalar|Param|null,
 *                         pool?: scalar|Param|null,
 *                     },
 *                     lock_path?: scalar|Param|null, // Default: "%kernel.cache_dir%/doctrine/orm/slc/filelock"
 *                     lock_lifetime?: scalar|Param|null, // Default: 60
 *                     type?: scalar|Param|null, // Default: "default"
 *                     lifetime?: scalar|Param|null, // Default: 0
 *                     service?: scalar|Param|null,
 *                     name?: scalar|Param|null,
 *                 }>,
 *                 loggers?: array<string, array{ // Default: []
 *                     name?: scalar|Param|null,
 *                     service?: scalar|Param|null,
 *                 }>,
 *             },
 *             hydrators?: array<string, scalar|Param|null>,
 *             mappings?: array<string, bool|string|array{ // Default: []
 *                 mapping?: scalar|Param|null, // Default: true
 *                 type?: scalar|Param|null,
 *                 dir?: scalar|Param|null,
 *                 alias?: scalar|Param|null,
 *                 prefix?: scalar|Param|null,
 *                 is_bundle?: bool|Param,
 *             }>,
 *             dql?: array{
 *                 string_functions?: array<string, scalar|Param|null>,
 *                 numeric_functions?: array<string, scalar|Param|null>,
 *                 datetime_functions?: array<string, scalar|Param|null>,
 *             },
 *             filters?: array<string, string|array{ // Default: []
 *                 class: scalar|Param|null,
 *                 enabled?: bool|Param, // Default: false
 *                 parameters?: array<string, mixed>,
 *             }>,
 *             identity_generation_preferences?: array<string, scalar|Param|null>,
 *         }>,
 *         resolve_target_entities?: array<string, scalar|Param|null>,
 *     },
 * }
 * @psalm-type DoctrineMigrationsConfig = array{
 *     migrations_paths?: array<string, scalar|Param|null>,
 *     services?: array<string, scalar|Param|null>,
 *     factories?: array<string, scalar|Param|null>,
 *     storage?: array{ // Storage to use for migration status metadata.
 *         table_storage?: array{ // The default metadata storage, implemented as a table in the database.
 *             table_name?: scalar|Param|null, // Default: null
 *             version_column_name?: scalar|Param|null, // Default: null
 *             version_column_length?: scalar|Param|null, // Default: null
 *             executed_at_column_name?: scalar|Param|null, // Default: null
 *             execution_time_column_name?: scalar|Param|null, // Default: null
 *         },
 *     },
 *     migrations?: list<scalar|Param|null>,
 *     connection?: scalar|Param|null, // Connection name to use for the migrations database. // Default: null
 *     em?: scalar|Param|null, // Entity manager name to use for the migrations database (available when doctrine/orm is installed). // Default: null
 *     all_or_nothing?: scalar|Param|null, // Run all migrations in a transaction. // Default: false
 *     check_database_platform?: scalar|Param|null, // Adds an extra check in the generated migrations to allow execution only on the same platform as they were initially generated on. // Default: true
 *     custom_template?: scalar|Param|null, // Custom template path for generated migration classes. // Default: null
 *     organize_migrations?: scalar|Param|null, // Organize migrations mode. Possible values are: "BY_YEAR", "BY_YEAR_AND_MONTH", false // Default: false
 *     enable_profiler?: bool|Param, // Whether or not to enable the profiler collector to calculate and visualize migration status. This adds some queries overhead. // Default: false
 *     transactional?: bool|Param, // Whether or not to wrap migrations in a single transaction. // Default: true
 * }
 * @psalm-type CmfRoutingConfig = array{
 *     chain?: array{
 *         routers_by_id?: array<string, scalar|Param|null>,
 *         replace_symfony_router?: bool|Param, // Default: true
 *     },
 *     dynamic?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         route_collection_limit?: scalar|Param|null, // Default: 0
 *         generic_controller?: scalar|Param|null, // Default: null
 *         default_controller?: scalar|Param|null, // Default: null
 *         controllers_by_type?: array<string, scalar|Param|null>,
 *         controllers_by_class?: array<string, scalar|Param|null>,
 *         templates_by_class?: array<string, scalar|Param|null>,
 *         persistence?: array{
 *             phpcr?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 manager_name?: scalar|Param|null, // Default: null
 *                 route_basepaths?: list<scalar|Param|null>,
 *                 enable_initializer?: bool|Param, // Default: true
 *             },
 *             orm?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 manager_name?: scalar|Param|null, // Default: null
 *                 route_class?: scalar|Param|null, // Default: "Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route"
 *             },
 *         },
 *         uri_filter_regexp?: scalar|Param|null, // Default: ""
 *         route_provider_service_id?: scalar|Param|null,
 *         route_filters_by_id?: array<string, scalar|Param|null>,
 *         content_repository_service_id?: scalar|Param|null,
 *         locales?: list<scalar|Param|null>,
 *         limit_candidates?: int|Param, // Default: 20
 *         match_implicit_locale?: bool|Param, // Default: true
 *         redirectable_url_matcher?: bool|Param, // Default: false
 *         auto_locale_pattern?: bool|Param, // Default: false
 *         url_generator?: scalar|Param|null, // URL generator service ID // Default: "cmf_routing.generator"
 *     },
 * }
 * @psalm-type SchebTwoFactorConfig = array{
 *     persister?: scalar|Param|null, // Default: "scheb_two_factor.persister.doctrine"
 *     model_manager_name?: scalar|Param|null, // Default: null
 *     security_tokens?: list<scalar|Param|null>,
 *     ip_whitelist?: list<scalar|Param|null>,
 *     ip_whitelist_provider?: scalar|Param|null, // Default: "scheb_two_factor.default_ip_whitelist_provider"
 *     two_factor_token_factory?: scalar|Param|null, // Default: "scheb_two_factor.default_token_factory"
 *     two_factor_provider_decider?: scalar|Param|null, // Default: "scheb_two_factor.default_provider_decider"
 *     two_factor_condition?: scalar|Param|null, // Default: null
 *     code_reuse_cache?: scalar|Param|null, // Default: null
 *     code_reuse_cache_duration?: int|Param, // Default: 60
 *     code_reuse_default_handler?: scalar|Param|null, // Default: null
 *     google?: bool|array{
 *         enabled?: scalar|Param|null, // Default: false
 *         form_renderer?: scalar|Param|null, // Default: null
 *         issuer?: scalar|Param|null, // Default: null
 *         server_name?: scalar|Param|null, // Default: null
 *         template?: scalar|Param|null, // Default: "@SchebTwoFactor/Authentication/form.html.twig"
 *         digits?: int|Param, // Default: 6
 *         leeway?: int|Param, // Default: 0
 *     },
 * }
 * @psalm-type FosJsRoutingConfig = array{
 *     serializer?: scalar|Param|null,
 *     routes_to_expose?: list<scalar|Param|null>,
 *     router?: scalar|Param|null, // Default: "router"
 *     request_context_base_url?: scalar|Param|null, // Default: null
 *     cache_control?: array{
 *         public?: bool|Param, // Default: false
 *         expires?: scalar|Param|null, // Default: null
 *         maxage?: scalar|Param|null, // Default: null
 *         smaxage?: scalar|Param|null, // Default: null
 *         vary?: list<scalar|Param|null>,
 *     },
 * }
 * @psalm-type FlysystemConfig = array{
 *     storages?: array<string, array{ // Default: []
 *         adapter: scalar|Param|null,
 *         options?: list<mixed>,
 *         visibility?: scalar|Param|null, // Default: null
 *         directory_visibility?: scalar|Param|null, // Default: null
 *         retain_visibility?: bool|Param|null, // Default: null
 *         case_sensitive?: bool|Param, // Default: true
 *         disable_asserts?: bool|Param, // Default: false
 *         public_url?: list<scalar|Param|null>,
 *         path_normalizer?: scalar|Param|null, // Default: null
 *         public_url_generator?: scalar|Param|null, // Default: null
 *         temporary_url_generator?: scalar|Param|null, // Default: null
 *         read_only?: bool|Param, // Default: false
 *     }>,
 * }
 * @psalm-type KnpPaginatorConfig = array{
 *     default_options?: array{
 *         sort_field_name?: scalar|Param|null, // Default: "sort"
 *         sort_direction_name?: scalar|Param|null, // Default: "direction"
 *         filter_field_name?: scalar|Param|null, // Default: "filterField"
 *         filter_value_name?: scalar|Param|null, // Default: "filterValue"
 *         page_name?: scalar|Param|null, // Default: "page"
 *         distinct?: bool|Param, // Default: true
 *         page_out_of_range?: scalar|Param|null, // Default: "ignore"
 *         default_limit?: scalar|Param|null, // Default: 10
 *     },
 *     template?: array{
 *         pagination?: scalar|Param|null, // Default: "@KnpPaginator/Pagination/sliding.html.twig"
 *         rel_links?: scalar|Param|null, // Default: "@KnpPaginator/Pagination/rel_links.html.twig"
 *         filtration?: scalar|Param|null, // Default: "@KnpPaginator/Pagination/filtration.html.twig"
 *         sortable?: scalar|Param|null, // Default: "@KnpPaginator/Pagination/sortable_link.html.twig"
 *     },
 *     page_range?: scalar|Param|null, // Default: 5
 *     page_limit?: scalar|Param|null, // Default: null
 *     convert_exception?: bool|Param, // Default: false
 *     remove_first_page_param?: bool|Param, // Default: false
 * }
 * @psalm-type CoreShopCoreConfig = array{
 *     send_usage_log?: scalar|Param|null, // Default: true
 *     checkout_manager_factory?: scalar|Param|null,
 *     after_logout_redirect_route?: scalar|Param|null, // Default: "coreshop_index"
 *     autoconfigure_with_attributes?: scalar|Param|null, // Default: false
 *     resources?: array{
 *         product_store_values?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|Param|null, // Default: "CoreShop\\Component\\Core\\Model\\ProductStoreValues"
 *                 interface?: scalar|Param|null, // Default: "CoreShop\\Component\\Core\\Model\\ProductStoreValuesInterface"
 *                 factory?: scalar|Param|null, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|Param|null, // Default: "CoreShop\\Bundle\\CoreBundle\\Doctrine\\ORM\\ProductStoreValuesRepository"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|Param|null>,
 *         css?: array{
 *             core?: scalar|Param|null, // Default: "/bundles/coreshopcore/pimcore/css/core.css"
 *             ...<mixed>
 *         },
 *         permissions?: scalar|Param|null, // Default: ["settings","ctc_assign_to_new","ctc_assign_to_existing"]
 *         editmode_js?: array<string, scalar|Param|null>,
 *         editmode_css?: array<string, scalar|Param|null>,
 *     },
 *     checkout: array<string, array{ // Default: []
 *         steps?: array<string, array{ // Default: []
 *             step: scalar|Param|null,
 *             priority: int|Param,
 *         }>,
 *     }>,
 * }
 * @psalm-type CoreShopStorageListConfig = array{
 *     list?: array<string, array{ // Default: []
 *         disable_caching?: bool|Param, // Default: false
 *         context?: array{
 *             interface?: scalar|Param|null, // Default: "CoreShop\\Component\\StorageList\\Context\\StorageListContextInterface"
 *             composite?: scalar|Param|null, // Default: "CoreShop\\Component\\StorageList\\Context\\CompositeStorageListContext"
 *             tag?: scalar|Param|null,
 *             restore_customer_list_only_on_login?: bool|Param, // Default: false
 *         },
 *         services?: array{
 *             manager?: scalar|Param|null, // Default: "CoreShop\\Component\\StorageList\\SessionStorageManager"
 *             modifier?: scalar|Param|null, // Default: "CoreShop\\Component\\StorageList\\StorageListModifierInterface"
 *             enable_default_store_based_decorator?: scalar|Param|null, // Default: false
 *             list_resolver?: scalar|Param|null, // Default: null
 *         },
 *         resource?: array{
 *             interface: scalar|Param|null,
 *             product_repository: scalar|Param|null,
 *             repository: scalar|Param|null,
 *             item_repository: scalar|Param|null,
 *             factory: scalar|Param|null,
 *             item_factory: scalar|Param|null,
 *             add_to_list_factory?: scalar|Param|null, // Default: "CoreShop\\Component\\StorageList\\Factory\\AddToStorageListFactory"
 *         },
 *         form?: array{
 *             type?: scalar|Param|null,
 *             add_type?: scalar|Param|null,
 *         },
 *         routes?: array{
 *             summary?: scalar|Param|null,
 *             index?: scalar|Param|null,
 *         },
 *         templates?: array{
 *             add_to_cart?: scalar|Param|null,
 *             summary?: scalar|Param|null,
 *         },
 *         session?: array{
 *             enabled?: bool|Param, // Default: false
 *             enable_logout_subscriber?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Default: "storage_list"
 *         },
 *         controller?: array{
 *             enabled?: bool|Param, // Default: false
 *             class?: scalar|Param|null, // Default: "CoreShop\\Bundle\\StorageListBundle\\Controller\\StorageListController"
 *         },
 *         multi_list?: array{
 *             enabled?: bool|Param, // Default: false
 *             controller?: array{
 *                 enabled?: bool|Param, // Default: false
 *                 class?: scalar|Param|null, // Default: "CoreShop\\Bundle\\StorageListBundle\\Controller\\StorageMultiListController"
 *             },
 *             templates?: array{
 *                 create_new_storage_list?: scalar|Param|null,
 *                 list_storage_list?: scalar|Param|null,
 *             },
 *             form?: array{
 *                 class?: scalar|Param|null,
 *             },
 *         },
 *         expiration?: array{
 *             enabled?: bool|Param, // Default: false
 *             service?: scalar|Param|null, // Default: null
 *             days?: int|Param, // Default: 14
 *             params?: mixed, // Default: []
 *         },
 *     }>,
 * }
 * @psalm-type WebpackEncoreConfig = array{
 *     output_path: scalar|Param|null, // The path where Encore is building the assets - i.e. Encore.setOutputPath()
 *     crossorigin?: false|"anonymous"|"use-credentials"|Param, // crossorigin value when Encore.enableIntegrityHashes() is used, can be false (default), anonymous or use-credentials // Default: false
 *     preload?: bool|Param, // preload all rendered script and link tags automatically via the http2 Link header. // Default: false
 *     cache?: bool|Param, // Enable caching of the entry point file(s) // Default: false
 *     strict_mode?: bool|Param, // Throw an exception if the entrypoints.json file is missing or an entry is missing from the data // Default: true
 *     builds?: array<string, scalar|Param|null>,
 *     script_attributes?: array<string, scalar|Param|null>,
 *     link_attributes?: array<string, scalar|Param|null>,
 * }
 * @psalm-type DebugConfig = array{
 *     max_items?: int|Param, // Max number of displayed items past the first level, -1 means no limit. // Default: 2500
 *     min_depth?: int|Param, // Minimum tree depth to clone all the items, 1 is default. // Default: 1
 *     max_string_length?: int|Param, // Max length of displayed strings, -1 means no limit. // Default: -1
 *     dump_destination?: scalar|Param|null, // A stream URL where dumps should be written to. // Default: null
 *     theme?: "dark"|"light"|Param, // Changes the color of the dump() output when rendered directly on the templating. "dark" (default) or "light". // Default: "dark"
 * }
 * @psalm-type WebProfilerConfig = array{
 *     toolbar?: bool|array{ // Profiler toolbar configuration
 *         enabled?: bool|Param, // Default: false
 *         ajax_replace?: bool|Param, // Replace toolbar on AJAX requests // Default: false
 *     },
 *     intercept_redirects?: bool|Param, // Default: false
 *     excluded_ajax_paths?: scalar|Param|null, // Default: "^/((index|app(_[\\w]+)?)\\.php/)?_wdt"
 * }
 * @psalm-type PimcoreElasticsearchClientConfig = array{
 *     es_clients?: array<string, array{ // Default: []
 *         name?: scalar|Param|null,
 *         hosts?: list<scalar|Param|null>,
 *         logger_channel?: scalar|Param|null, // Logger channel to be used for elasticsearch client logs // Default: "pimcore.elasticsearch.default"
 *         username?: scalar|Param|null, // Username for elasticsearch authentication
 *         password?: scalar|Param|null, // Password for elasticsearch authentication
 *         ca_bundle?: scalar|Param|null, // Path to certificate authority file (.crt)
 *         ssl_key?: scalar|Param|null, // Path to private SSL key file (.key)
 *         ssl_cert?: scalar|Param|null, // Path to PEM formatted SSL cert file (.cert)
 *         ssl_password?: scalar|Param|null, // If private key and certificate require a password (default: null)
 *         ssl_verification?: bool|Param, // Enable or disable the SSL verification (default: true)
 *         http_options?: list<scalar|Param|null>,
 *         cloud_id?: scalar|Param|null, // Elastic Cloud Id for elasticsearch cloud authentication
 *         api_key?: scalar|Param|null, // Elastic Cloud API key for elasticsearch cloud authentication
 *     }>,
 * }
 * @psalm-type MercureConfig = array{
 *     hubs?: array<string, array{ // Default: []
 *         url?: scalar|Param|null, // URL of the hub's publish endpoint
 *         public_url?: scalar|Param|null, // URL of the hub's public endpoint // Default: null
 *         jwt?: string|array{ // JSON Web Token configuration.
 *             value?: scalar|Param|null, // JSON Web Token to use to publish to this hub.
 *             provider?: scalar|Param|null, // The ID of a service to call to provide the JSON Web Token.
 *             factory?: scalar|Param|null, // The ID of a service to call to create the JSON Web Token.
 *             publish?: list<scalar|Param|null>,
 *             subscribe?: list<scalar|Param|null>,
 *             secret?: scalar|Param|null, // The JWT Secret to use.
 *             passphrase?: scalar|Param|null, // The JWT secret passphrase. // Default: ""
 *             algorithm?: scalar|Param|null, // The algorithm to use to sign the JWT // Default: "hmac.sha256"
 *         },
 *         jwt_provider?: scalar|Param|null, // Deprecated: The child node "jwt_provider" at path "mercure.hubs..jwt_provider" is deprecated, use "jwt.provider" instead. // The ID of a service to call to generate the JSON Web Token.
 *         bus?: scalar|Param|null, // Name of the Messenger bus where the handler for this hub must be registered. Default to the default bus if Messenger is enabled.
 *     }>,
 *     default_hub?: scalar|Param|null,
 *     default_cookie_lifetime?: int|Param, // Default lifetime of the cookie containing the JWT, in seconds. Defaults to the value of "framework.session.cookie_lifetime". // Default: null
 *     enable_profiler?: bool|Param, // Deprecated: The child node "enable_profiler" at path "mercure.enable_profiler" is deprecated. // Enable Symfony Web Profiler integration.
 * }
 * @psalm-type KnpMenuConfig = array{
 *     providers?: array{
 *         builder_alias?: bool|Param, // Default: true
 *     },
 *     twig?: array{
 *         template?: scalar|Param|null, // Default: "@KnpMenu/menu.html.twig"
 *     },
 *     templating?: bool|Param, // Default: false
 *     default_renderer?: scalar|Param|null, // Default: "twig"
 * }
 * @psalm-type PimcoreConfig = array{
 *     bundles?: array{ // Define parameters for Pimcore Bundle Locator
 *         search_paths?: list<scalar|Param|null>,
 *         handle_composer?: bool|Param, // Define whether it should be scanning bundles through composer /vendor folder or not // Default: true
 *     },
 *     flags?: list<scalar|Param|null>,
 *     translations?: array{
 *         domains?: list<scalar|Param|null>,
 *         admin_translation_mapping?: array<string, scalar|Param|null>,
 *         debugging?: bool|array{ // If debugging is enabled, the translator will return the plain translation key instead of the translated message.
 *             enabled?: bool|Param, // Default: true
 *             parameter?: scalar|Param|null, // Default: "pimcore_debug_translations"
 *         },
 *     },
 *     maps?: array{
 *         tile_layer_url_template?: scalar|Param|null, // Default: "https://a.tile.openstreetmap.org/{z}/{x}/{y}.png"
 *         geocoding_url_template?: scalar|Param|null, // Default: "https://nominatim.openstreetmap.org/search?q={q}&addressdetails=1&format=json&limit=1"
 *         reverse_geocoding_url_template?: scalar|Param|null, // Default: "https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&addressdetails=1"
 *     },
 *     general?: array{
 *         timezone?: scalar|Param|null, // Default: ""
 *         path_variable?: scalar|Param|null, // Additional $PATH variable (: separated) (/x/y:/foo/bar): // Default: null
 *         domain?: scalar|Param|null, // Default: ""
 *         redirect_to_maindomain?: bool|Param, // Default: false
 *         language?: scalar|Param|null, // Deprecated: The child node "language" at path "pimcore.general.language" is deprecated. // Default: "en"
 *         valid_languages?: list<scalar|Param|null>,
 *         required_languages?: list<scalar|Param|null>,
 *         fallback_languages?: list<scalar|Param|null>,
 *         default_language?: scalar|Param|null, // Default: "en"
 *         disable_usage_statistics?: bool|Param, // Default: false
 *         debug_admin_translations?: bool|Param, // Debug Admin-Translations (text in UI will be displayed wrapped in +) // Default: false
 *     },
 *     maintenance?: array{
 *         housekeeping?: array{
 *             cleanup_tmp_files_atime_older_than?: int|Param, // Integer value in seconds. // Default: 86400
 *             cleanup_profiler_files_atime_older_than?: int|Param, // Integer value in seconds. // Default: 1800
 *         },
 *     },
 *     objects?: array{
 *         ignore_localized_query_fallback?: bool|Param, // Default: false
 *         tree_paging_limit?: int|Param, // Default: 30
 *         auto_save_interval?: int|Param, // Default: 60
 *         versions?: array{
 *             days?: scalar|Param|null, // Default: null
 *             steps?: scalar|Param|null, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         custom_layouts?: array{
 *             definitions?: list<array{ // Default: []
 *                 id?: scalar|Param|null,
 *                 name?: scalar|Param|null,
 *                 description?: scalar|Param|null, // Default: null
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *                 userOwner?: int|Param,
 *                 userModification?: int|Param,
 *                 classId?: scalar|Param|null,
 *                 default?: int|Param,
 *                 layoutDefinitions?: mixed,
 *             }>,
 *         },
 *         select_options?: array{
 *             definitions?: list<array{ // Default: []
 *                 id?: scalar|Param|null,
 *                 group?: scalar|Param|null,
 *                 adminOnly?: bool|Param, // Default: false
 *                 useTraits?: scalar|Param|null,
 *                 implementsInterfaces?: scalar|Param|null,
 *                 selectOptions?: list<array{ // Default: []
 *                     value?: scalar|Param|null,
 *                     label?: scalar|Param|null,
 *                     name?: scalar|Param|null,
 *                 }>,
 *             }>,
 *         },
 *         class_definitions?: array{
 *             data?: array{
 *                 map?: array<string, scalar|Param|null>,
 *                 prefixes?: list<scalar|Param|null>,
 *             },
 *             layout?: array{
 *                 map?: array<string, scalar|Param|null>,
 *                 prefixes?: list<scalar|Param|null>,
 *             },
 *         },
 *         ...<mixed>
 *     },
 *     assets?: array{
 *         thumbnails?: array{
 *             allowed_formats?: list<scalar|Param|null>,
 *             max_scaling_factor?: float|Param, // Default: 5.0
 *         },
 *         frontend_prefixes?: array{
 *             source?: scalar|Param|null, // Default: ""
 *             thumbnail?: scalar|Param|null, // Default: ""
 *             thumbnail_deferred?: scalar|Param|null, // Default: ""
 *         },
 *         preview_image_thumbnail?: scalar|Param|null, // Default: null
 *         default_upload_path?: scalar|Param|null, // Default: "_default_upload_bucket"
 *         tree_paging_limit?: int|Param, // Default: 100
 *         image?: array{
 *             max_pixels?: int|Param, // Maximum number of pixels an image can have when added (width × height). // Default: 40000000
 *             low_quality_image_preview?: bool|array{ // Allow a LQIP SVG image to be generated alongside any other thumbnails.
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             thumbnails?: array{
 *                 definitions?: list<array{ // Default: []
 *                     id?: scalar|Param|null,
 *                     name?: scalar|Param|null,
 *                     description?: scalar|Param|null,
 *                     group?: scalar|Param|null,
 *                     format?: scalar|Param|null,
 *                     quality?: scalar|Param|null,
 *                     highResolution?: scalar|Param|null,
 *                     preserveColor?: bool|Param,
 *                     preserveMetaData?: bool|Param,
 *                     rasterizeSVG?: bool|Param,
 *                     useCropBox?: bool|Param,
 *                     downloadable?: bool|Param,
 *                     forceProcessICCProfiles?: bool|Param,
 *                     modificationDate?: int|Param,
 *                     creationDate?: int|Param,
 *                     preserveAnimation?: bool|Param,
 *                     items?: list<array{ // Default: []
 *                         method?: scalar|Param|null,
 *                         arguments?: list<mixed>,
 *                     }>,
 *                     medias?: list<list<array{ // Default: []
 *                             method?: scalar|Param|null,
 *                             arguments?: list<mixed>,
 *                         }>>,
 *                 }>,
 *                 clip_auto_support?: bool|Param, // Try to detect and use clipping paths and masks in images when generating thumbnails. // Default: true
 *                 max_srcset_dpi_factor?: int|Param, // Maximum generated srcset DPI factor for web images. // Default: 2
 *                 image_optimizers?: bool|array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 auto_formats?: list<bool|array{ // Default: {"avif":{"enabled":true,"quality":50},"webp":{"enabled":true,"quality":null}}
 *                     enabled?: bool|Param, // Default: true
 *                     quality?: scalar|Param|null,
 *                 }>,
 *                 status_cache?: bool|Param, // Store image metadata such as filename and modification date in assets_image_thumbnail_cache, this is helpful when using remote object storage for thumbnails. // Default: true
 *                 auto_clear_temp_files?: bool|Param, // Automatically delete all image thumbnail files any time an image or its metadata is updated. // Default: true
 *             },
 *         },
 *         video?: array{
 *             thumbnails?: array{
 *                 definitions?: list<array{ // Default: []
 *                     id?: scalar|Param|null,
 *                     name?: scalar|Param|null,
 *                     description?: scalar|Param|null,
 *                     group?: scalar|Param|null,
 *                     videoBitrate?: scalar|Param|null,
 *                     audioBitrate?: scalar|Param|null,
 *                     quality?: scalar|Param|null,
 *                     modificationDate?: int|Param,
 *                     creationDate?: int|Param,
 *                     items?: list<array{ // Default: []
 *                         method?: scalar|Param|null,
 *                         arguments?: list<mixed>,
 *                     }>,
 *                     medias?: list<list<array{ // Default: []
 *                             method?: scalar|Param|null,
 *                             arguments?: list<mixed>,
 *                         }>>,
 *                 }>,
 *                 auto_clear_temp_files?: bool|Param, // Automatically delete all video thumbnail files any time an image or its metadata is updated. // Default: true
 *             },
 *         },
 *         document?: array{
 *             thumbnails?: array{
 *                 enabled?: bool|Param, // Process thumbnails for Asset documents. // Default: true
 *             },
 *             process_page_count?: bool|Param, // Process & store page count for Asset documents. Internally required for thumbnails & text generation // Default: true
 *             process_text?: bool|Param, // Process text for Asset documents (e.g. used by backend search). // Default: true
 *             scan_pdf?: bool|Param, // Scan PDF documents for unsafe JavaScript. // Default: true
 *             open_pdf_in_new_tab?: "all-pdfs"|"only-unsafe"|"none"|Param, // Default: "only-unsafe"
 *         },
 *         versions?: array{
 *             days?: scalar|Param|null, // Default: null
 *             steps?: scalar|Param|null, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             use_hardlinks?: bool|Param, // Default: true
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         icc_rgb_profile?: scalar|Param|null, // Absolute path to default ICC RGB profile (if no embedded profile is given) // Default: null
 *         icc_cmyk_profile?: scalar|Param|null, // Absolute path to default ICC CMYK profile (if no embedded profile is given) // Default: null
 *         metadata?: array{
 *             alt?: scalar|Param|null, // Set to replace the default metadata used for auto alt functionality in frontend // Default: ""
 *             copyright?: scalar|Param|null, // Set to replace the default metadata used for copyright in frontend // Default: ""
 *             title?: scalar|Param|null, // Set to replace the default metadata used for title in frontend // Default: ""
 *             predefined?: array{
 *                 definitions?: list<array{ // Default: []
 *                     name?: scalar|Param|null,
 *                     description?: scalar|Param|null,
 *                     group?: scalar|Param|null,
 *                     language?: scalar|Param|null,
 *                     type?: scalar|Param|null,
 *                     data?: scalar|Param|null,
 *                     targetSubtype?: scalar|Param|null,
 *                     config?: scalar|Param|null,
 *                     inheritable?: bool|Param,
 *                     creationDate?: int|Param,
 *                     modificationDate?: int|Param,
 *                 }>,
 *             },
 *             class_definitions?: array{
 *                 data?: array{
 *                     map?: array<string, scalar|Param|null>,
 *                     prefixes?: list<scalar|Param|null>,
 *                 },
 *             },
 *         },
 *         type_definitions?: array{
 *             map?: list<array{ // Default: []
 *                 class?: scalar|Param|null,
 *                 matching?: list<scalar|Param|null>,
 *             }>,
 *         },
 *     },
 *     documents?: array{
 *         doc_types?: array{
 *             definitions?: list<array{ // Default: []
 *                 name?: scalar|Param|null,
 *                 group?: scalar|Param|null,
 *                 module?: scalar|Param|null,
 *                 controller?: scalar|Param|null,
 *                 template?: scalar|Param|null,
 *                 type?: scalar|Param|null,
 *                 priority?: int|Param,
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *                 staticGeneratorEnabled?: bool|Param, // Default: false
 *             }>,
 *         },
 *         versions?: array{
 *             days?: scalar|Param|null, // Default: null
 *             steps?: scalar|Param|null, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         default_controller?: scalar|Param|null, // Default: "App\\Controller\\DefaultController::defaultAction"
 *         error_pages?: array{
 *             default?: scalar|Param|null, // Default: null
 *             localized?: list<scalar|Param|null>,
 *         },
 *         allow_trailing_slash?: scalar|Param|null, // Default: "no"
 *         generate_preview?: bool|Param, // Default: false
 *         preview_url_prefix?: scalar|Param|null, // Default: ""
 *         tree_paging_limit?: int|Param, // Default: 50
 *         editables?: array{
 *             map?: array<string, scalar|Param|null>,
 *             prefixes?: list<scalar|Param|null>,
 *         },
 *         areas?: array{
 *             autoload?: bool|Param, // Default: true
 *         },
 *         auto_save_interval?: int|Param, // Default: 60
 *         static_page_router?: array{
 *             enabled?: bool|Param, // Enable Static Page router for document when using remote storage for generated pages // Default: false
 *             route_pattern?: scalar|Param|null, // Optionally define route patterns to lookup static pages. Regular Expressions like: /^\/en\/Magazine/ // Default: null
 *         },
 *         static_page_generator?: array{
 *             use_main_domain?: bool|Param, // Use main domain for static pages folder in tmp/pages // Default: false
 *             headers?: list<array{ // Default: []
 *                 name?: scalar|Param|null,
 *                 value?: scalar|Param|null,
 *             }>,
 *         },
 *         type_definitions?: array{
 *             map?: list<array{ // Default: []
 *                 class?: scalar|Param|null,
 *                 translatable?: bool|Param, // Default: true
 *                 valid_table?: scalar|Param|null, // Default: null
 *                 direct_route?: bool|Param, // Default: false
 *                 translatable_inheritance?: bool|Param, // Default: true
 *                 children_supported?: bool|Param, // Default: true
 *                 only_printable_childrens?: bool|Param, // Default: false
 *                 predefined_document_types?: bool|Param, // Default: false
 *             }>,
 *         },
 *         ...<mixed>
 *     },
 *     encryption?: array{
 *         secret?: scalar|Param|null, // Default: null
 *     },
 *     models?: array{
 *         class_overrides?: array<string, scalar|Param|null>,
 *     },
 *     routing?: array{
 *         static?: array{
 *             locale_params?: list<scalar|Param|null>,
 *         },
 *     },
 *     full_page_cache?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         lifetime?: scalar|Param|null, // Optional output-cache lifetime (in seconds) after the cache expires, if not defined the cache will be cleaned on every action inside the CMS, otherwise not (for high traffic sites) // Default: null
 *         exclude_patterns?: scalar|Param|null, // Regular Expressions like: /^\/dir\/toexclude/
 *         exclude_cookie?: scalar|Param|null, // Comma separated list of cookie names, that will automatically disable the full-page cache
 *         ...<mixed>
 *     },
 *     context?: array<string, array{ // Default: []
 *         routes?: list<null|array{ // Default: []
 *             path?: scalar|Param|null, // Default: false
 *             route?: scalar|Param|null, // Default: false
 *             host?: scalar|Param|null, // Default: false
 *             methods?: list<scalar|Param|null>,
 *         }>,
 *     }>,
 *     web_profiler?: array{
 *         toolbar?: array{
 *             excluded_routes?: list<null|array{ // Default: []
 *                 path?: scalar|Param|null, // Default: false
 *                 route?: scalar|Param|null, // Default: false
 *                 host?: scalar|Param|null, // Default: false
 *                 methods?: list<scalar|Param|null>,
 *             }>,
 *         },
 *     },
 *     security?: array{
 *         password?: array{
 *             algorithm?: "2y"|"argon2i"|"argon2id"|Param, // The hashing algorithm to use for backend users and objects containing a "password" field. // Default: "2y"
 *             options?: list<mixed>,
 *         },
 *         factory_type?: "encoder"|"password_hasher"|Param, // Default: "encoder"
 *         encoder_factories?: array<string, string|array{ // Default: []
 *             id?: scalar|Param|null,
 *         }>,
 *         password_hasher_factories?: array<string, string|array{ // Default: []
 *             id?: scalar|Param|null,
 *         }>,
 *     },
 *     email?: array{
 *         sender?: array{
 *             name?: scalar|Param|null, // Default: ""
 *             email?: scalar|Param|null, // Default: ""
 *         },
 *         return?: array{
 *             name?: scalar|Param|null, // Default: ""
 *             email?: scalar|Param|null, // Default: ""
 *         },
 *         debug?: array{
 *             email_addresses?: scalar|Param|null, // Default: ""
 *         },
 *         usespecific?: scalar|Param|null, // Default: false
 *     },
 *     workflows?: array<string, array{ // Default: []
 *         placeholders?: list<scalar|Param|null>,
 *         custom_extensions?: array<mixed>,
 *         enabled?: bool|Param, // Can be used to enable or disable the workflow. // Default: true
 *         priority?: int|Param, // When multiple custom view or permission settings from different places in different workflows are valid, the workflow with the highest priority will be used. // Default: 0
 *         label?: scalar|Param|null, // Will be used in the backend interface as nice name for the workflow. If not set the technical workflow name will be used as label too.
 *         audit_trail?: bool|array{ // Enable default audit trail feature provided by Symfony. Take a look at the Symfony docs for more details.
 *             enabled?: bool|Param, // Default: false
 *         },
 *         type?: "workflow"|"state_machine"|Param, // A workflow with type "workflow" can handle multiple places at one time whereas a state_machine provides a finite state_machine (only one place at one time). Take a look at the Symfony docs for more details.
 *         marking_store?: array{ // Handles the way how the state/place is stored. If not defined "state_table" will be used as default. Take a look at @TODO for a description of the different types.
 *             type?: "multiple_state"|"single_state"|"state_table"|"data_object_multiple_state"|"data_object_splitted_state"|Param,
 *             arguments?: list<mixed>,
 *             service?: scalar|Param|null,
 *         },
 *         supports?: list<scalar|Param|null>,
 *         support_strategy?: array{ // Can be used to implement a special logic which subjects are supported by the workflow. For example only products matching certain criteria.
 *             type?: "expression"|Param, // Type "expression": a symfony expression to define a criteria.
 *             arguments?: list<mixed>,
 *             service?: scalar|Param|null, // Define a custom service to handle the logic. Take a look at the Symfony docs for more details.
 *         },
 *         initial_markings?: list<scalar|Param|null>,
 *         places?: list<array{ // Default: []
 *             label?: scalar|Param|null, // Nice name which will be used in the Pimcore backend.
 *             title?: scalar|Param|null, // Title/tooltip for this place when it is displayed in the header of the Pimcore element detail view in the backend. // Default: ""
 *             color?: scalar|Param|null, // Color of the place which will be used in the Pimcore backend. // Default: "#bfdadc"
 *             colorInverted?: bool|Param, // If set to true the color will be used as border and font color otherwise as background color. // Default: false
 *             visibleInHeader?: bool|Param, // If set to false, the place will be hidden in the header of the Pimcore element detail view in the backend. // Default: true
 *             permissions?: list<array{ // Default: []
 *                 condition?: scalar|Param|null, // A symfony expression can be configured here. The first set of permissions which are matching the condition will be used.
 *                 save?: bool|Param, // save permission as it can be configured in Pimcore workplaces
 *                 publish?: bool|Param, // publish permission as it can be configured in Pimcore workplaces
 *                 unpublish?: bool|Param, // unpublish permission as it can be configured in Pimcore workplaces
 *                 delete?: bool|Param, // delete permission as it can be configured in Pimcore workplaces
 *                 rename?: bool|Param, // rename permission as it can be configured in Pimcore workplaces
 *                 view?: bool|Param, // view permission as it can be configured in Pimcore workplaces
 *                 settings?: bool|Param, // settings permission as it can be configured in Pimcore workplaces
 *                 versions?: bool|Param, // versions permission as it can be configured in Pimcore workplaces
 *                 properties?: bool|Param, // properties permission as it can be configured in Pimcore workplaces
 *                 modify?: bool|Param, // a short hand for save, publish, unpublish, delete + rename
 *                 objectLayout?: scalar|Param|null, // if set, the user will see the configured custom data object layout
 *             }>,
 *         }>,
 *         transitions: list<array{ // Default: []
 *             name: scalar|Param|null,
 *             guard?: scalar|Param|null, // An expression to block the transition
 *             from?: list<scalar|Param|null>,
 *             to?: list<scalar|Param|null>,
 *             options?: array{
 *                 label?: scalar|Param|null, // Nice name for the Pimcore backend.
 *                 notes?: array{
 *                     commentEnabled?: bool|Param, // If enabled a detail window will open when the user executes the transition. In this detail view the user be asked to enter a "comment". This comment then will be used as comment for the notes/events feature. // Default: false
 *                     commentRequired?: bool|Param, // Set this to true if the comment should be a required field. // Default: false
 *                     commentSetterFn?: scalar|Param|null, // Can be used for data objects. The comment will be saved to the data object additionally to the notes/events through this setter function.
 *                     commentGetterFn?: scalar|Param|null, // Can be used for data objects to prefill the comment field with data from the data object.
 *                     type?: scalar|Param|null, // Set's the type string in the saved note. // Default: "Status update"
 *                     title?: scalar|Param|null, // An optional alternative "title" for the note, if blank the actions transition result is used.
 *                     additionalFields?: list<array{ // Default: []
 *                         name: scalar|Param|null, // The technical name used in the input form.
 *                         fieldType: "input"|"numeric"|"textarea"|"select"|"datetime"|"date"|"user"|"checkbox"|Param, // The data component name/field type.
 *                         title?: scalar|Param|null, // The label used by the field
 *                         required?: bool|Param, // Whether or not the field is required. // Default: false
 *                         setterFn?: scalar|Param|null, // Optional setter function (available in the element, for example in the updated object), if not specified, data will be added to notes. The Workflow manager will call the function with the whole field data.
 *                         fieldTypeSettings?: list<mixed>,
 *                     }>,
 *                     customHtml?: array{
 *                         position?: "top"|"center"|"bottom"|Param, // Set position of custom HTML inside modal (top, center, bottom). // Default: "top"
 *                         service?: scalar|Param|null, // Define a custom service for rendering custom HTML within the note modal.
 *                     },
 *                 },
 *                 iconClass?: scalar|Param|null, // CSS class to define the icon which will be used in the actions button in the backend.
 *                 objectLayout?: scalar|Param|null, // Forces an object layout after the transition was performed. This objectLayout setting overrules all objectLayout settings within the places configs. // Default: false
 *                 notificationSettings?: list<array{ // Default: []
 *                     condition?: scalar|Param|null, // A symfony expression can be configured here. All sets of notification which are matching the condition will be used.
 *                     notifyUsers?: list<scalar|Param|null>,
 *                     notifyRoles?: list<scalar|Param|null>,
 *                     channelType?: list<"mail"|"pimcore_notification"|Param>,
 *                     mailType?: "template"|"pimcore_document"|Param, // Type of mail source. // Default: "template"
 *                     mailPath?: scalar|Param|null, // Path to mail source - either Symfony path to template or fullpath to Pimcore document. Optional use %_locale% as placeholder for language. // Default: "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig"
 *                 }>,
 *                 changePublishedState?: "no_change"|"force_unpublished"|"force_published"|"save_version"|Param, // Change published state of element while transition (only available for documents and data objects). // Default: "no_change"
 *                 unsavedChangesBehaviour?: "save"|"warn"|"ignore"|Param, // Behaviour when workflow transition gets applied but there are unsaved changes // Default: "warn"
 *             },
 *         }>,
 *         globalActions?: list<array{ // Default: []
 *             label?: scalar|Param|null, // Nice name for the Pimcore backend.
 *             iconClass?: scalar|Param|null, // CSS class to define the icon which will be used in the actions button in the backend.
 *             objectLayout?: scalar|Param|null, // Forces an object layout after the global action was performed. This objectLayout setting overrules all objectLayout settings within the places configs. // Default: false
 *             guard?: scalar|Param|null, // An expression to block the action
 *             saveSubject?: bool|Param, // Determines if the global action should perform a save on the subject, default behavior is set to true // Default: true
 *             to?: list<scalar|Param|null>,
 *             notes?: array{ // See notes section of transitions. It works exactly the same way.
 *                 commentEnabled?: bool|Param, // Default: false
 *                 commentRequired?: bool|Param, // Default: false
 *                 commentSetterFn?: scalar|Param|null,
 *                 commentGetterFn?: scalar|Param|null,
 *                 type?: scalar|Param|null, // Default: "Status update"
 *                 title?: scalar|Param|null,
 *                 additionalFields?: list<array{ // Default: []
 *                     name: scalar|Param|null,
 *                     fieldType: "input"|"textarea"|"select"|"datetime"|"date"|"user"|"checkbox"|Param,
 *                     title?: scalar|Param|null,
 *                     required?: bool|Param, // Default: false
 *                     setterFn?: scalar|Param|null,
 *                     fieldTypeSettings?: list<mixed>,
 *                 }>,
 *                 customHtml?: array{
 *                     position?: "top"|"center"|"bottom"|Param, // Set position of custom HTML inside modal (top, center, bottom). // Default: "top"
 *                     service?: scalar|Param|null, // Define a custom service for rendering custom HTML within the note modal.
 *                 },
 *             },
 *         }>,
 *     }>,
 *     httpclient?: array{
 *         adapter?: scalar|Param|null, // Set to `Proxy` if proxy server should be used // Default: "Socket"
 *         proxy_host?: scalar|Param|null, // Default: null
 *         proxy_port?: scalar|Param|null, // Default: null
 *         proxy_user?: scalar|Param|null, // Default: null
 *         proxy_pass?: scalar|Param|null, // Default: null
 *     },
 *     applicationlog?: array{
 *         loggers?: array{
 *             db?: array{
 *                 min_level_or_list?: mixed, // Default: "debug"
 *                 max_level?: scalar|Param|null, // Default: "emergency"
 *             },
 *         },
 *         mail_notification?: array{
 *             send_log_summary?: bool|Param, // Send log summary via email // Default: false
 *             filter_priority?: scalar|Param|null, // Filter threshold for email summary, choose one of: 8 (debug),7 (info),6 (notice),5 (warning),4 (error),3 (critical),2 (alert),1 (emerg). You can use the integer or the string representation. // Default: null
 *             mail_receiver?: scalar|Param|null, // Log summary receivers. Separate multiple email receivers by using ;
 *         },
 *         archive_treshold?: scalar|Param|null, // Archive threshold in days // Default: 30
 *         archive_alternative_database?: scalar|Param|null, // Archive database name (optional). Tables will get archived to a different database, recommended when huge amounts of logs will be generated // Default: ""
 *         archive_db_table_storage_engine?: scalar|Param|null, // DB storage engine to be used for archive tables (e.g. ARCHIVE, InnoDB, Aria, ...) // Default: "archive"
 *         delete_archive_threshold?: scalar|Param|null, // Threshold for deleting application log archive tables (in months) // Default: "6"
 *     },
 *     properties?: array{
 *         predefined?: array{
 *             definitions?: list<array{ // Default: []
 *                 name?: scalar|Param|null,
 *                 description?: scalar|Param|null,
 *                 key?: scalar|Param|null,
 *                 type?: scalar|Param|null,
 *                 data?: scalar|Param|null,
 *                 config?: scalar|Param|null,
 *                 ctype?: scalar|Param|null,
 *                 inheritable?: bool|Param,
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *             }>,
 *         },
 *         ...<mixed>
 *     },
 *     perspectives?: array{
 *         definitions?: list<array{ // Default: []
 *             iconCls?: scalar|Param|null,
 *             icon?: scalar|Param|null,
 *             toolbar?: mixed,
 *             dashboards?: array{
 *                 disabledPortlets?: mixed,
 *                 predefined?: mixed,
 *             },
 *             elementTree?: list<array{ // Default: []
 *                 type?: scalar|Param|null,
 *                 position?: scalar|Param|null,
 *                 name?: scalar|Param|null,
 *                 expanded?: bool|Param,
 *                 hidden?: scalar|Param|null,
 *                 sort?: int|Param,
 *                 id?: scalar|Param|null,
 *                 treeContextMenu?: mixed,
 *             }>,
 *         }>,
 *         ...<mixed>
 *     },
 *     custom_views?: array{
 *         definitions?: list<array{ // Default: []
 *             id?: scalar|Param|null,
 *             treetype?: scalar|Param|null,
 *             name?: scalar|Param|null,
 *             condition?: scalar|Param|null,
 *             icon?: scalar|Param|null,
 *             rootfolder?: scalar|Param|null,
 *             showroot?: scalar|Param|null,
 *             classes?: mixed,
 *             position?: scalar|Param|null,
 *             sort?: scalar|Param|null,
 *             expanded?: bool|Param,
 *             having?: scalar|Param|null,
 *             where?: scalar|Param|null,
 *             treeContextMenu?: mixed,
 *             joins?: list<array{ // Default: []
 *                 type?: scalar|Param|null,
 *                 condition?: scalar|Param|null,
 *                 name?: mixed,
 *                 columns?: mixed,
 *             }>,
 *         }>,
 *         ...<mixed>
 *     },
 *     templating_engine?: array{
 *         twig?: array{
 *             sandbox_security_policy?: array{ // Allowlist tags, filters & functions for evaluating twig templates in a sandbox environment e.g. used by Mailer & Text layout component.
 *                 tags?: list<scalar|Param|null>,
 *                 filters?: list<scalar|Param|null>,
 *                 functions?: list<scalar|Param|null>,
 *             },
 *         },
 *     },
 *     gotenberg?: array{
 *         base_url?: scalar|Param|null, // Default: "http://gotenberg:3000"
 *         ping_cache_ttl?: scalar|Param|null, // Default: 60
 *     },
 *     dependency?: array{
 *         enabled?: scalar|Param|null, // Default: true
 *     },
 *     product_registration?: array{
 *         instance_identifier?: scalar|Param|null, // Unique identifier of that Pimcore instance. Will be generated during install.
 *         product_key?: scalar|Param|null, // Product registration key obtained during product registration. It is based on `instance_identifier` and `pimcore.encryption.secret`.
 *     },
 *     config_location?: array{
 *         image_thumbnails?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         video_thumbnails?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         document_types?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         predefined_properties?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         predefined_asset_metadata?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         perspectives?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         custom_views?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         object_custom_layouts?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *         },
 *         system_settings?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *             read_target?: array{
 *                 type?: "symfony-config"|"settings-store"|Param, // Default: null
 *                 options?: list<mixed>,
 *             },
 *         },
 *         select_options?: array{
 *             write_target?: array{
 *                 type?: "symfony-config"|"settings-store"|"disabled"|Param, // Default: "symfony-config"
 *                 options?: list<mixed>,
 *             },
 *             read_target?: array{
 *                 type?: "symfony-config"|"settings-store"|Param, // Default: null
 *                 options?: list<mixed>,
 *             },
 *         },
 *     },
 * }
 * @psalm-type ConfigType = array{
 *     imports?: ImportsConfig,
 *     parameters?: ParametersConfig,
 *     services?: ServicesConfig,
 *     pimcore_static_routes?: PimcoreStaticRoutesConfig,
 *     pimcore_open_search_client?: PimcoreOpenSearchClientConfig,
 *     pimcore_studio_ui?: PimcoreStudioUiConfig,
 *     pimcore_studio_backend?: PimcoreStudioBackendConfig,
 *     pimcore_generic_data_index?: PimcoreGenericDataIndexConfig,
 *     pimcore_generic_execution_engine?: PimcoreGenericExecutionEngineConfig,
 *     pimcore_custom_reports?: PimcoreCustomReportsConfig,
 *     core_shop_menu?: CoreShopMenuConfig,
 *     jms_serializer?: JmsSerializerConfig,
 *     core_shop_pimcore?: CoreShopPimcoreConfig,
 *     core_shop_locale?: CoreShopLocaleConfig,
 *     core_shop_resource?: CoreShopResourceConfig,
 *     core_shop_seo?: CoreShopSeoConfig,
 *     core_shop_money?: CoreShopMoneyConfig,
 *     core_shop_workflow?: CoreShopWorkflowConfig,
 *     core_shop_messenger?: CoreShopMessengerConfig,
 *     core_shop_rule?: CoreShopRuleConfig,
 *     core_shop_configuration?: CoreShopConfigurationConfig,
 *     core_shop_order?: CoreShopOrderConfig,
 *     core_shop_customer?: CoreShopCustomerConfig,
 *     core_shop_user?: CoreShopUserConfig,
 *     core_shop_inventory?: CoreShopInventoryConfig,
 *     core_shop_variant?: CoreShopVariantConfig,
 *     core_shop_product?: CoreShopProductConfig,
 *     core_shop_theme?: CoreShopThemeConfig,
 *     core_shop_address?: CoreShopAddressConfig,
 *     core_shop_currency?: CoreShopCurrencyConfig,
 *     core_shop_taxation?: CoreShopTaxationConfig,
 *     core_shop_store?: CoreShopStoreConfig,
 *     core_shop_index?: CoreShopIndexConfig,
 *     core_shop_shipping?: CoreShopShippingConfig,
 *     core_shop_payment?: CoreShopPaymentConfig,
 *     core_shop_sequence?: CoreShopSequenceConfig,
 *     core_shop_payum_payment?: CoreShopPayumPaymentConfig,
 *     core_shop_notification?: CoreShopNotificationConfig,
 *     core_shop_tracking?: CoreShopTrackingConfig,
 *     core_shop_frontend?: CoreShopFrontendConfig,
 *     core_shop_payum?: CoreShopPayumConfig,
 *     core_shop_product_quantity_price_rules?: CoreShopProductQuantityPriceRulesConfig,
 *     core_shop_wishlist?: CoreShopWishlistConfig,
 *     core_shop_class_definition_patch?: CoreShopClassDefinitionPatchConfig,
 *     payum?: PayumConfig,
 *     stof_doctrine_extensions?: StofDoctrineExtensionsConfig,
 *     sylius_theme?: SyliusThemeConfig,
 *     pimcore_google_marketing?: PimcoreGoogleMarketingConfig,
 *     framework?: FrameworkConfig,
 *     security?: SecurityConfig,
 *     twig?: TwigConfig,
 *     twig_extra?: TwigExtraConfig,
 *     monolog?: MonologConfig,
 *     doctrine?: DoctrineConfig,
 *     doctrine_migrations?: DoctrineMigrationsConfig,
 *     cmf_routing?: CmfRoutingConfig,
 *     scheb_two_factor?: SchebTwoFactorConfig,
 *     fos_js_routing?: FosJsRoutingConfig,
 *     flysystem?: FlysystemConfig,
 *     knp_paginator?: KnpPaginatorConfig,
 *     core_shop_core?: CoreShopCoreConfig,
 *     core_shop_storage_list?: CoreShopStorageListConfig,
 *     webpack_encore?: WebpackEncoreConfig,
 *     debug?: DebugConfig,
 *     web_profiler?: WebProfilerConfig,
 *     pimcore_elasticsearch_client?: PimcoreElasticsearchClientConfig,
 *     mercure?: MercureConfig,
 *     knp_menu?: KnpMenuConfig,
 *     pimcore?: PimcoreConfig,
 *     "when@dev"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         pimcore_static_routes?: PimcoreStaticRoutesConfig,
 *         pimcore_open_search_client?: PimcoreOpenSearchClientConfig,
 *         pimcore_studio_ui?: PimcoreStudioUiConfig,
 *         pimcore_studio_backend?: PimcoreStudioBackendConfig,
 *         pimcore_generic_data_index?: PimcoreGenericDataIndexConfig,
 *         pimcore_generic_execution_engine?: PimcoreGenericExecutionEngineConfig,
 *     },
 *     ...<string, ExtensionType|array{ // extra keys must follow the when@%env% pattern or match an extension alias
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         ...<string, ExtensionType>,
 *     }>
 * }
 */
final class App
{
    /**
     * @param ConfigType $config
     *
     * @psalm-return ConfigType
     */
    public static function config(array $config): array
    {
        return AppReference::config($config);
    }
}

namespace Symfony\Component\Routing\Loader\Configurator;

/**
 * This class provides array-shapes for configuring the routes of an application.
 *
 * Example:
 *
 *     ```php
 *     // config/routes.php
 *     namespace Symfony\Component\Routing\Loader\Configurator;
 *
 *     return Routes::config([
 *         'controllers' => [
 *             'resource' => 'routing.controllers',
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type RouteConfig = array{
 *     path: string|array<string,string>,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type ImportConfig = array{
 *     resource: string,
 *     type?: string,
 *     exclude?: string|list<string>,
 *     prefix?: string|array<string,string>,
 *     name_prefix?: string,
 *     trailing_slash_on_root?: bool,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type AliasConfig = array{
 *     alias: string,
 *     deprecated?: array{package:string, version:string, message?:string},
 * }
 * @psalm-type RoutesConfig = array{
 *     "when@dev"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     ...<string, RouteConfig|ImportConfig|AliasConfig>
 * }
 */
final class Routes
{
    /**
     * @param RoutesConfig $config
     *
     * @psalm-return RoutesConfig
     */
    public static function config(array $config): array
    {
        return $config;
    }
}
