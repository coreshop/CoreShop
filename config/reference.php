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
 * @psalm-type ParametersConfig = array<string, scalar|\UnitEnum|array<scalar|\UnitEnum|array<mixed>|null>|null>
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
 * @psalm-type AiConfig = array{
 *     platform?: array{
 *         albert?: array{
 *             api_key: string|Param,
 *             base_url: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         anthropic?: array{
 *             api_key: string|Param,
 *             version?: string|Param, // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         azure?: array<string, array{ // Default: []
 *             api_key: string|Param,
 *             base_url: string|Param,
 *             deployment: string|Param,
 *             api_version?: string|Param, // The used API version
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         }>,
 *         bedrock?: array<string, array{ // Default: []
 *             bedrock_runtime_client?: string|Param, // Service ID of the Bedrock runtime client to use // Default: null
 *             model_catalog?: string|Param, // Default: null
 *         }>,
 *         cache?: array<string, array{ // Default: []
 *             platform: string|Param,
 *             service?: string|Param, // The cache service id as defined under the "cache" configuration key // Default: "cache.app"
 *             cache_key?: string|Param, // Key used to store platform results, if not set, the current platform name will be used, the "prompt_cache_key" can be set during platform call to override this value
 *             ttl?: int|Param,
 *         }>,
 *         cartesia?: array{
 *             api_key: string|Param,
 *             version: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         cerebras?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         decart?: array{
 *             api_key: string|Param,
 *             host?: string|Param, // Default: "https://api.decart.ai/v1"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         dockermodelrunner?: array{
 *             host_url?: string|Param, // Default: "http://127.0.0.1:12434"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         elevenlabs?: array{
 *             api_key: string|Param,
 *             host?: string|Param, // Default: "https://api.elevenlabs.io/v1"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             api_catalog?: bool|Param, // If set, the ElevenLabs API will be used to build the catalog and retrieve models information, using this option leads to additional HTTP calls
 *         },
 *         failover?: array<string, array{ // Default: []
 *             platforms?: list<scalar|null|Param>,
 *             rate_limiter?: string|Param,
 *         }>,
 *         gemini?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         generic?: array<string, array{ // Default: []
 *             base_url: string|Param,
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             model_catalog?: string|Param, // Service ID of the model catalog to use
 *             supports_completions?: bool|Param, // Default: true
 *             supports_embeddings?: bool|Param, // Default: true
 *             completions_path?: string|Param, // Default: "/v1/chat/completions"
 *             embeddings_path?: string|Param, // Default: "/v1/embeddings"
 *         }>,
 *         huggingface?: array{
 *             api_key: string|Param,
 *             provider?: string|Param, // Default: "hf-inference"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         lmstudio?: array{
 *             host_url?: string|Param, // Default: "http://127.0.0.1:1234"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         mistral?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         ollama?: array{
 *             host_url?: string|Param, // Default: "http://127.0.0.1:11434"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             api_catalog?: bool|Param, // If set, the Ollama API will be used to build the catalog and retrieve models information, using this option leads to additional HTTP calls
 *         },
 *         openai?: array{
 *             api_key: string|Param,
 *             region?: scalar|null|Param, // The region for OpenAI API (EU, US, or null for default) // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         openrouter?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         perplexity?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         scaleway?: array{
 *             api_key: scalar|null|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         transformersphp?: array<mixed>,
 *         vertexai?: array{
 *             location: string|Param,
 *             project_id: string|Param,
 *             api_key?: string|Param, // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         voyage?: array{
 *             api_key: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *     },
 *     model?: array<string, array<string, array{ // Default: []
 *             class?: string|Param, // The fully qualified class name of the model (must extend Symfony\AI\Platform\Model) // Default: "Symfony\\AI\\Platform\\Model"
 *             capabilities?: list<value-of<\Symfony\AI\Platform\Capability>|\Symfony\AI\Platform\Capability|Param>,
 *         }>>,
 *     agent?: array<string, array{ // Default: []
 *         platform?: string|Param, // Service name of platform // Default: "Symfony\\AI\\Platform\\PlatformInterface"
 *         model?: mixed,
 *         memory?: mixed, // Memory configuration: string for static memory, or array with "service" key for service reference // Default: null
 *         prompt?: string|array{ // The system prompt configuration
 *             text?: string|Param, // The system prompt text
 *             file?: string|Param, // Path to file containing the system prompt
 *             include_tools?: bool|Param, // Include tool definitions at the end of the system prompt // Default: false
 *             enable_translation?: bool|Param, // Enable translation for the system prompt // Default: false
 *             translation_domain?: string|Param, // The translation domain for the system prompt // Default: null
 *         },
 *         tools?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             services?: list<string|array{ // Default: []
 *                 service?: string|Param,
 *                 agent?: string|Param,
 *                 name?: string|Param,
 *                 description?: string|Param,
 *                 method?: string|Param,
 *             }>,
 *         },
 *         keep_tool_messages?: bool|Param, // Keep tool messages in the conversation history // Default: false
 *         include_sources?: bool|Param, // Include sources exposed by tools as part of the tool result metadata // Default: false
 *         fault_tolerant_toolbox?: bool|Param, // Continue the agent run even if a tool call fails // Default: true
 *     }>,
 *     multi_agent?: array<string, array{ // Default: []
 *         orchestrator: string|Param, // Service ID of the orchestrator agent
 *         handoffs: array<string, list<scalar|null|Param>>,
 *         fallback: string|Param, // Service ID of the fallback agent for unmatched requests
 *     }>,
 *     store?: array{
 *         azuresearch?: array<string, array{ // Default: []
 *             endpoint: string|Param,
 *             api_key: string|Param,
 *             index_name: string|Param,
 *             api_version: string|Param,
 *             vector_field?: string|Param,
 *         }>,
 *         cache?: array<string, array{ // Default: []
 *             service?: string|Param, // Default: "cache.app"
 *             cache_key?: string|Param, // The name of the store will be used if the key is not set
 *             strategy?: string|Param,
 *         }>,
 *         chromadb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "Codewithkyrian\\ChromaDB\\Client"
 *             collection: string|Param,
 *         }>,
 *         clickhouse?: array<string, array{ // Default: []
 *             dsn?: string|Param,
 *             http_client?: string|Param,
 *             database: string|Param,
 *             table: string|Param,
 *         }>,
 *         cloudflare?: array<string, array{ // Default: []
 *             account_id?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *             dimensions?: int|Param, // Default: 1536
 *             metric?: string|Param, // Default: "cosine"
 *             endpoint?: string|Param,
 *         }>,
 *         elasticsearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *             vectors_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             similarity?: string|Param, // Default: "cosine"
 *             http_client?: string|Param, // Default: "http_client"
 *         }>,
 *         manticoresearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             table?: string|Param,
 *             field?: string|Param, // Default: "_vectors"
 *             type?: string|Param, // Default: "hnsw"
 *             similarity?: string|Param, // Default: "cosine"
 *             dimensions?: int|Param, // Default: 1536
 *             quantization?: string|Param,
 *         }>,
 *         mariadb?: array<string, array{ // Default: []
 *             connection?: string|Param,
 *             table_name?: string|Param,
 *             index_name?: string|Param,
 *             vector_field_name?: string|Param,
 *             setup_options?: array{
 *                 dimensions?: int|Param,
 *             },
 *         }>,
 *         meilisearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *             embedder?: string|Param, // Default: "default"
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             semantic_ratio?: float|Param, // The ratio between semantic (vector) and full-text search (0.0 to 1.0). Default: 1.0 (100% semantic) // Default: 1.0
 *         }>,
 *         memory?: array<string, array{ // Default: []
 *             strategy?: string|Param,
 *         }>,
 *         milvus?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key: string|Param,
 *             database?: string|Param,
 *             collection: string|Param,
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             metric_type?: string|Param, // Default: "COSINE"
 *         }>,
 *         mongodb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "MongoDB\\Client"
 *             database: string|Param,
 *             collection?: string|Param,
 *             index_name: string|Param,
 *             vector_field?: string|Param, // Default: "vector"
 *             bulk_write?: bool|Param, // Default: false
 *         }>,
 *         neo4j?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             database?: string|Param,
 *             vector_index_name?: string|Param,
 *             node_name?: string|Param,
 *             vector_field?: string|Param, // Default: "embeddings"
 *             dimensions?: int|Param, // Default: 1536
 *             distance?: string|Param, // Default: "cosine"
 *             quantization?: bool|Param,
 *         }>,
 *         opensearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *             vectors_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             space_type?: string|Param, // Default: "l2"
 *             http_client?: string|Param, // Default: "http_client"
 *         }>,
 *         pinecone?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "Probots\\Pinecone\\Client"
 *             index_name: string|Param,
 *             namespace?: string|Param,
 *             filter?: list<scalar|null|Param>,
 *             top_k?: int|Param,
 *         }>,
 *         postgres?: array<string, array{ // Default: []
 *             dsn?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             table_name?: string|Param,
 *             vector_field?: string|Param, // Default: "embedding"
 *             distance?: "cosine"|"inner_product"|"l1"|"l2"|Param, // Distance metric to use for vector similarity search // Default: "l2"
 *             dbal_connection?: string|Param,
 *         }>,
 *         qdrant?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             collection_name?: string|Param,
 *             dimensions?: int|Param, // Default: 1536
 *             distance?: string|Param, // Default: "Cosine"
 *             async?: bool|Param,
 *         }>,
 *         redis?: array<string, array{ // Default: []
 *             connection_parameters?: mixed, // see https://github.com/phpredis/phpredis?tab=readme-ov-file#example-1
 *             client?: string|Param, // a service id of a Redis client
 *             index_name?: string|Param,
 *             key_prefix?: string|Param, // Default: "vector:"
 *             distance?: "COSINE"|"L2"|"IP"|Param, // Distance metric to use for vector similarity search // Default: "COSINE"
 *         }>,
 *         supabase?: array<string, array{ // Default: []
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             url: string|Param,
 *             api_key: string|Param,
 *             table?: string|Param,
 *             vector_field?: string|Param, // Default: "embedding"
 *             vector_dimension?: int|Param, // Default: 1536
 *             function_name?: string|Param, // Default: "match_documents"
 *         }>,
 *         surrealdb?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             namespace?: string|Param,
 *             database?: string|Param,
 *             table?: string|Param,
 *             vector_field?: string|Param, // Default: "_vectors"
 *             strategy?: string|Param, // Default: "cosine"
 *             dimensions?: int|Param, // Default: 1536
 *             namespaced_user?: bool|Param,
 *         }>,
 *         typesense?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key: string|Param,
 *             collection?: string|Param,
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *         }>,
 *         weaviate?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key: string|Param,
 *             collection?: string|Param,
 *         }>,
 *     },
 *     message_store?: array{
 *         cache?: array<string, array{ // Default: []
 *             service?: string|Param, // Default: "cache.app"
 *             key?: string|Param, // The name of the message store will be used if the key is not set
 *             ttl?: int|Param,
 *         }>,
 *         cloudflare?: array<string, array{ // Default: []
 *             account_id?: string|Param,
 *             api_key?: string|Param,
 *             namespace?: string|Param,
 *             endpoint_url?: string|Param, // If the version of the Cloudflare API is updated, use this key to support it.
 *         }>,
 *         doctrine?: array{
 *             dbal?: array<string, array{ // Default: []
 *                 connection?: string|Param,
 *                 table_name?: string|Param, // The name of the message store will be used if the table_name is not set
 *             }>,
 *         },
 *         meilisearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *         }>,
 *         memory?: array<string, array{ // Default: []
 *             identifier?: string|Param,
 *         }>,
 *         mongodb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "MongoDB\\Client"
 *             database: string|Param,
 *             collection: string|Param,
 *         }>,
 *         pogocache?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             password?: string|Param,
 *             key?: string|Param,
 *         }>,
 *         redis?: array<string, array{ // Default: []
 *             connection_parameters?: mixed, // see https://github.com/phpredis/phpredis?tab=readme-ov-file#example-1
 *             client?: string|Param, // a service id of a Redis client
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *         }>,
 *         session?: array<string, array{ // Default: []
 *             identifier?: string|Param,
 *         }>,
 *         surrealdb?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             namespace?: string|Param,
 *             database?: string|Param,
 *             table?: string|Param,
 *             namespaced_user?: bool|Param, // Using a namespaced user is a good practice to prevent any undesired access to a specific table, see https://surrealdb.com/docs/surrealdb/reference-guide/security-best-practices
 *         }>,
 *     },
 *     chat?: array<string, array{ // Default: []
 *         agent?: string|Param,
 *         message_store?: string|Param,
 *     }>,
 *     vectorizer?: array<string, array{ // Default: []
 *         platform?: string|Param, // Service name of platform // Default: "Symfony\\AI\\Platform\\PlatformInterface"
 *         model?: mixed,
 *     }>,
 *     indexer?: array<string, array{ // Default: []
 *         loader: string|Param, // Service name of loader
 *         source?: mixed, // Source identifier (file path, URL, etc.) or array of sources // Default: null
 *         transformers?: list<scalar|null|Param>,
 *         filters?: list<scalar|null|Param>,
 *         vectorizer?: scalar|null|Param, // Service name of vectorizer // Default: "Symfony\\AI\\Store\\Document\\VectorizerInterface"
 *         store?: string|Param, // Service name of store // Default: "Symfony\\AI\\Store\\StoreInterface"
 *     }>,
 *     retriever?: array<string, array{ // Default: []
 *         vectorizer?: scalar|null|Param, // Service name of vectorizer // Default: "Symfony\\AI\\Store\\Document\\VectorizerInterface"
 *         store?: string|Param, // Service name of store // Default: "Symfony\\AI\\Store\\StoreInterface"
 *     }>,
 * }
 * @psalm-type PimcoreSeoConfig = array{
 *     sitemaps?: array{
 *         generators?: array<string, bool|string|array{ // Default: []
 *             enabled?: bool|Param, // Default: true
 *             generator_id?: scalar|null|Param,
 *             priority?: int|Param, // Default: 0
 *         }>,
 *     },
 *     redirects?: array{
 *         status_codes?: list<scalar|null|Param>,
 *         auto_create_redirects?: bool|Param, // Auto create redirects on moving documents & changing pretty url, updating Url slugs in Data Objects. // Default: false
 *     },
 * }
 * @psalm-type PimcoreStaticRoutesConfig = array{
 *     definitions?: list<array{ // Default: []
 *         name?: scalar|null|Param,
 *         pattern?: scalar|null|Param,
 *         reverse?: scalar|null|Param,
 *         controller?: scalar|null|Param,
 *         variables?: scalar|null|Param,
 *         defaults?: scalar|null|Param,
 *         siteId?: list<int|Param>,
 *         methods?: list<scalar|null|Param>,
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
 * @psalm-type PimcoreNewsletterConfig = array{
 *     sender?: array{
 *         name?: scalar|null|Param,
 *         email?: scalar|null|Param,
 *     },
 *     return?: array{
 *         name?: scalar|null|Param,
 *         email?: scalar|null|Param,
 *     },
 *     method?: scalar|null|Param, // Default: null
 *     debug?: array{
 *         email_addresses?: scalar|null|Param, // Default: ""
 *     },
 *     use_specific?: bool|Param, // Default: false
 *     source_adapters?: array<string, scalar|null|Param>,
 *     default_url_prefix?: scalar|null|Param, // Default: null
 * }
 * @psalm-type PimcoreOpenSearchClientConfig = array{
 *     clients?: array<string, array{ // Default: []
 *         name?: scalar|null|Param,
 *         hosts?: list<scalar|null|Param>,
 *         logger_channel?: scalar|null|Param, // Logger channel to be used for opensearch client logs // Default: "pimcore.opensearch.default"
 *         log_404_errors?: bool|Param, // Enables logging of 404 errors (default: false) // Default: false
 *         username?: scalar|null|Param, // Username for opensearch authentication // Default: "admin"
 *         password?: scalar|null|Param, // Password for opensearch authentication // Default: "admin"
 *         ssl_key?: scalar|null|Param, // Path to private SSL key file (.key)
 *         ssl_cert?: scalar|null|Param, // Path to PEM formatted SSL cert file (.cert)
 *         ssl_password?: scalar|null|Param, // If private key and certificate require a password (default: null)
 *         ssl_verification?: bool|Param, // Enable or disable the SSL verification (default: true)
 *         aws_region?: scalar|null|Param, // Will set the setSigV4Region()
 *         aws_service?: scalar|null|Param, // Will set the setSigV4ServicesetSigV4Service()
 *         aws_key?: scalar|null|Param, // Will set the setSigV4CredentialProvider() key
 *         aws_secret?: scalar|null|Param, // Will set the setSigV4CredentialProvider() key
 *     }>,
 * }
 * @psalm-type PimcoreStudioUiConfig = array{
 *     url_path?: scalar|null|Param, // Default: "/pimcore-studio"
 *     static_resources?: array{
 *         css?: list<scalar|null|Param>,
 *         js?: list<scalar|null|Param>,
 *         editmode?: array{
 *             css?: list<scalar|null|Param>,
 *             js?: list<scalar|null|Param>,
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
 *         exclude_paths?: list<scalar|null|Param>,
 *         additional_urls?: array{
 *             default-src?: list<scalar|null|Param>,
 *             img-src?: list<scalar|null|Param>,
 *             script-src?: list<scalar|null|Param>,
 *             style-src?: list<scalar|null|Param>,
 *             connect-src?: list<scalar|null|Param>,
 *             font-src?: list<scalar|null|Param>,
 *             media-src?: list<scalar|null|Param>,
 *             frame-src?: list<scalar|null|Param>,
 *         },
 *     },
 * }
 * @psalm-type PimcoreStudioBackendConfig = array{
 *     url_prefix?: scalar|null|Param, // Default: "/pimcore-studio/api"
 *     open_api_scan_paths?: list<scalar|null|Param>,
 *     api_token?: array{
 *         lifetime?: int|Param, // Default: 3600
 *     },
 *     allowed_hosts_for_cors?: list<scalar|null|Param>,
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
 *         hub_url_server?: scalar|null|Param, // The url to the mercure hub for the server.This can also be the docker container name (e.g., http://mercure/.well-known/mercure). If it is not set, default will be set to "http(s)://<YOUR_PIMCORE_MAIN_DOMAIN>/hub/.well-known/mercure". // Default: null
 *         hub_url_client?: scalar|null|Param, // The url to the mercure hub for the (frontend) client. If it is not set, the default will be set to "http(s)://<YOUR_CURRENT_PIMCORE_HOST>/hub". It is possible to use "<PIMCORE_SCHEMA_HOST>" as a placeholder for the current schema and host if path to mercure should be different. // Default: null
 *         jwt_key: scalar|null|Param, // The key used to sign the JWT token. Must be longer than 256 bits. // Default: "some-secret-default"
 *         cookie_lifetime?: int|Param, // Lifetime of the mercure cookie in seconds. Default is one hour. // Default: 3600
 *         jwt_cookie_host?: scalar|null|Param, // Domain where to set the Mercure auth cookie, e.g. ".example.com". // Default: null
 *         jwt_cookie_strictness?: bool|Param, // If true, use SameSite=Strict; if false, use SameSite=None. // Default: true
 *     },
 *     asset_download_settings?: array{
 *         size_limit?: int|Param, // The maximum size of all assets together that can be downloaded in bytes. // Default: 5368709120
 *         amount_limit?: int|Param, // The maximum amount of assets that can be downloaded at once. // Default: 1000
 *     },
 *     csv_settings?: array{
 *         default_delimiter?: scalar|null|Param, // Default delimiter to be used for csv operations. // Default: ";"
 *     },
 *     grid?: array{
 *         asset?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|null|Param,
 *                 key?: scalar|null|Param,
 *             }>,
 *         },
 *         data_object?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|null|Param,
 *                 key?: scalar|null|Param,
 *             }>,
 *             skip_field_types?: list<scalar|null|Param>,
 *         },
 *     },
 *     search_grid?: array{
 *         asset?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|null|Param,
 *                 key?: scalar|null|Param,
 *             }>,
 *         },
 *         data_object?: array{
 *             predefined_columns?: list<array{ // Default: []
 *                 group?: scalar|null|Param,
 *                 key?: scalar|null|Param,
 *             }>,
 *         },
 *     },
 *     notes?: array{
 *         types?: array{ // List all note types for asset, document, and data-object.
 *             asset?: list<scalar|null|Param>,
 *             document?: list<scalar|null|Param>,
 *             data-object?: list<scalar|null|Param>,
 *         },
 *     },
 *     asset_metadata_adapter_mapping?: array<string, list<scalar|null|Param>>,
 *     data_object_data_adapter_mapping?: array<string, list<scalar|null|Param>>,
 *     document_type_adapter_mapping?: array<string, list<scalar|null|Param>>,
 *     user?: array{
 *         default_key_bindings?: list<array{ // Default: []
 *             key: scalar|null|Param,
 *             action: scalar|null|Param,
 *             alt?: scalar|null|Param, // Default: false
 *             ctrl?: scalar|null|Param, // Default: false
 *             shift?: scalar|null|Param, // Default: false
 *         }>,
 *     },
 *     open_api_servers?: list<array{ // Default: []
 *         url: scalar|null|Param, // The URL to the server.
 *         description: scalar|null|Param, // A description of the server.
 *     }>,
 *     widget_types?: list<scalar|null|Param>,
 *     studio_perspectives?: array<string, array{ // Default: []
 *         name: scalar|null|Param,
 *         icon: null|array{
 *             type?: "name"|"path"|Param,
 *             value?: scalar|null|Param,
 *         },
 *         widgetsLeft?: array<string, scalar|null|Param>,
 *         widgetsRight?: array<string, scalar|null|Param>,
 *         widgetsBottom?: array<string, scalar|null|Param>,
 *         expandedLeft?: scalar|null|Param, // The id of the widget that should be expanded on the left side. // Default: null
 *         expandedRight?: scalar|null|Param, // The id of the widget that should be expanded on the right side. // Default: null
 *         contextPermissions?: array<string, array<string, scalar|null|Param>>,
 *     }>,
 *     element_tree_widgets?: array<string, array{ // Default: []
 *         name: scalar|null|Param,
 *         elementType?: scalar|null|Param, // Default: "object"
 *         pageSize?: scalar|null|Param, // Default: null
 *         icon: null|array{
 *             type?: "name"|"path"|Param,
 *             value?: scalar|null|Param,
 *         },
 *         rootFolder: scalar|null|Param, // Default: "/"
 *         showRoot?: bool|Param, // Default: false
 *         classes?: list<scalar|null|Param>,
 *         pql?: scalar|null|Param, // Default: null
 *         contextPermissions?: list<scalar|null|Param>,
 *     }>,
 *     studio_from_default_email?: scalar|null|Param, // Default: "studio-admin@pimcore.com"
 *     twig?: array{ // Configure the Twig sandbox policy.
 *         sandbox_security_policy?: array{
 *             tags?: list<scalar|null|Param>,
 *             filters?: list<scalar|null|Param>,
 *             functions?: list<scalar|null|Param>,
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
 *             client_name?: scalar|null|Param, // Name of search client from to be used. // Default: "default"
 *             client_type?: "openSearch"|"elasticsearch"|Param, // Type of search client to be used. // Default: "openSearch"
 *             index_prefix?: scalar|null|Param, // Default: "pimcore_"
 *         },
 *         search_settings?: array{
 *             list_page_size?: scalar|null|Param, // Default: 60
 *             list_max_filter_options?: scalar|null|Param, // Default: 500
 *             max_synchronous_children_rename_limit?: scalar|null|Param, // Maximum number of direct/synchronous children path updates if asset folders get renamed. If more then the given number of children need an path update the process will be done by the asynchronous index update command. This mechanismn is needed to be able to see directly the new paths in the folder navigation. // Default: 500
 *             search_analyzer_attributes?: array<string, array{ // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *         },
 *         index_settings?: mixed, // Default: []
 *         queue_settings?: array{
 *             worker_count?: scalar|null|Param, // Default: 1
 *             min_batch_size?: scalar|null|Param, // Default: 5
 *             max_batch_size?: scalar|null|Param, // Default: 400
 *         },
 *         system_fields_settings?: array{
 *             general?: array<string, array{ // Default: []
 *                 type: scalar|null|Param,
 *                 analyzer?: scalar|null|Param,
 *                 ignore_above?: scalar|null|Param,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             document?: array<string, array{ // Default: []
 *                 type: scalar|null|Param,
 *                 analyzer?: scalar|null|Param,
 *                 ignore_above?: scalar|null|Param,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             data_object?: array<string, array{ // Default: []
 *                 type: scalar|null|Param,
 *                 analyzer?: scalar|null|Param,
 *                 ignore_above?: scalar|null|Param,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *             asset?: array<string, array{ // Default: []
 *                 type: scalar|null|Param,
 *                 analyzer?: scalar|null|Param,
 *                 ignore_above?: scalar|null|Param,
 *                 properties?: mixed, // Default: []
 *                 fields?: mixed, // Default: []
 *             }>,
 *         },
 *     },
 * }
 * @psalm-type PimcoreGenericExecutionEngineConfig = array{
 *     error_handling?: "continue_on_error"|"stop_on_first_error"|Param, // Specifies how errors should be handled for all job run executions. // Default: "continue_on_error"
 *     execution_context?: list<array{ // Default: []
 *         translations_domain?: scalar|null|Param, // Translation domain which should be used by the job run. Default value is "admin". // Default: "admin"
 *         error_handling?: "continue_on_error"|"stop_on_first_error"|Param, // Error handling behavior which should be used by the job run. Overrides the global value.
 *     }>,
 * }
 * @psalm-type PimcoreAdminConfig = array{
 *     gdpr_data_extractor?: array{
 *         dataObjects?: array{ // Settings for DataObjects DataProvider
 *             classes?: list<array{ // MY_CLASS_NAME: include: true allowDelete: false includedRelations: - manualSegemens - calculatedSegments // Default: []
 *                 include?: bool|Param, // Set if class should be considered in export. // Default: true
 *                 allowDelete?: bool|Param, // Allow delete of objects directly in preview grid. // Default: false
 *                 includedRelations?: list<scalar|null|Param>,
 *             }>,
 *         },
 *         assets?: array{ // Settings for Assets DataProvider
 *             types?: list<array{ // asset types // Default: []
 *             }>,
 *         },
 *     },
 *     objects?: array{
 *         notes_events?: array{
 *             types?: list<scalar|null|Param>,
 *         },
 *     },
 *     assets?: array{
 *         notes_events?: array{
 *             types?: list<scalar|null|Param>,
 *         },
 *         hide_edit_image?: bool|Param, // Default: false
 *         disable_tree_preview?: bool|Param, // Default: true
 *     },
 *     documents?: array{
 *         notes_events?: array{
 *             types?: list<scalar|null|Param>,
 *         },
 *         email_search?: list<scalar|null|Param>,
 *     },
 *     notifications?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         check_new_notification?: bool|array{ // Can be used to enable or disable the check of new notifications (url: /admin/notification/find-last-unread).
 *             enabled?: bool|Param, // Default: true
 *             interval?: int|Param, // Interval in seconds to check new notifications // Default: 30
 *         },
 *     },
 *     user?: array{
 *         default_key_bindings?: list<array{ // Default: []
 *             key: scalar|null|Param,
 *             action: scalar|null|Param,
 *             alt?: scalar|null|Param, // Default: false
 *             ctrl?: scalar|null|Param, // Default: false
 *             shift?: scalar|null|Param, // Default: false
 *         }>,
 *     },
 *     admin_languages?: list<scalar|null|Param>,
 *     csrf_protection?: array{
 *         excluded_routes?: list<scalar|null|Param>,
 *     },
 *     admin_csp_header?: bool|array{ // Can be used to enable or disable the Content Security Policy headers.
 *         enabled?: bool|Param, // Default: true
 *         exclude_paths?: list<scalar|null|Param>,
 *         additional_urls?: array{
 *             default-src?: list<scalar|null|Param>,
 *             img-src?: list<scalar|null|Param>,
 *             script-src?: list<scalar|null|Param>,
 *             style-src?: list<scalar|null|Param>,
 *             connect-src?: list<scalar|null|Param>,
 *             font-src?: list<scalar|null|Param>,
 *             media-src?: list<scalar|null|Param>,
 *             frame-src?: list<scalar|null|Param>,
 *         },
 *     },
 *     custom_admin_path_identifier?: scalar|null|Param, // Default: null
 *     custom_admin_route_name?: scalar|null|Param, // Default: "my_custom_admin_entry_point"
 *     branding?: array{
 *         login_screen_invert_colors?: bool|Param, // Default: false
 *         color_login_screen?: scalar|null|Param, // Default: null
 *         color_admin_interface?: scalar|null|Param, // Default: null
 *         color_admin_interface_background?: scalar|null|Param, // Default: null
 *         login_screen_custom_image?: scalar|null|Param, // Default: ""
 *     },
 *     session?: array{
 *         attribute_bags?: array<string, array{ // Default: []
 *             storage_key?: scalar|null|Param, // Default: null
 *         }>,
 *     },
 *     translations?: array{
 *         path?: scalar|null|Param, // Default: null
 *     },
 *     security_firewall?: mixed,
 *     config_location?: array{
 *         admin_system_settings?: array{
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
 * @psalm-type PimcoreCustomReportsConfig = array{
 *     definitions?: list<array{ // Default: []
 *         id?: scalar|null|Param,
 *         name?: scalar|null|Param,
 *         niceName?: scalar|null|Param,
 *         sql?: scalar|null|Param,
 *         group?: scalar|null|Param,
 *         groupIconClass?: scalar|null|Param,
 *         iconClass?: scalar|null|Param,
 *         menuShortcut?: bool|Param,
 *         reportClass?: scalar|null|Param,
 *         chartType?: scalar|null|Param,
 *         pieColumn?: scalar|null|Param,
 *         pieLabelColumn?: scalar|null|Param,
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
 *     adapters?: array<string, scalar|null|Param>,
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
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 * }
 * @psalm-type JmsSerializerConfig = array{
 *     twig_enabled?: scalar|null|Param, // Default: "default"
 *     profiler?: scalar|null|Param, // Default: true
 *     enum_support?: scalar|null|Param, // Default: false
 *     default_value_property_reader_support?: scalar|null|Param, // Default: false
 *     handlers?: array{
 *         datetime?: array{
 *             default_format?: scalar|null|Param, // Default: "Y-m-d\\TH:i:sP"
 *             default_deserialization_formats?: list<scalar|null|Param>,
 *             default_timezone?: scalar|null|Param, // Default: "Europe/Berlin"
 *             cdata?: scalar|null|Param, // Default: true
 *         },
 *         array_collection?: array{
 *             initialize_excluded?: bool|Param, // Default: false
 *         },
 *         symfony_uid?: array{
 *             default_format?: scalar|null|Param, // Default: "canonical"
 *             cdata?: scalar|null|Param, // Default: true
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
 *         id?: scalar|null|Param,
 *         separator?: scalar|null|Param, // Default: "_"
 *         lower_case?: bool|Param, // Default: true
 *     },
 *     expression_evaluator?: string|array{
 *         id?: scalar|null|Param, // Default: "jms_serializer.expression_evaluator"
 *     },
 *     metadata?: array{
 *         warmup?: array{
 *             paths?: array{
 *                 included?: list<scalar|null|Param>,
 *                 excluded?: list<scalar|null|Param>,
 *             },
 *         },
 *         cache?: scalar|null|Param, // Default: "file"
 *         debug?: bool|Param, // Default: true
 *         file_cache?: array{
 *             dir?: scalar|null|Param, // Default: null
 *         },
 *         include_interfaces?: bool|Param, // Default: false
 *         auto_detection?: bool|Param, // Default: true
 *         infer_types_from_doc_block?: bool|Param, // Default: false
 *         infer_types_from_doctrine_metadata?: bool|Param, // Infers type information from Doctrine metadata if no explicit type has been defined for a property. // Default: true
 *         directories?: array<string, array{ // Default: []
 *             path: scalar|null|Param,
 *             namespace_prefix?: scalar|null|Param, // Default: ""
 *         }>,
 *     },
 *     visitors?: array{
 *         json_serialization?: array{
 *             depth?: scalar|null|Param,
 *             options?: scalar|null|Param, // Default: 1024
 *         },
 *         json_deserialization?: array{
 *             options?: scalar|null|Param, // Default: 0
 *             strict?: bool|Param, // Default: false
 *         },
 *         xml_serialization?: array{
 *             version?: scalar|null|Param,
 *             encoding?: scalar|null|Param,
 *             format_output?: bool|Param, // Default: false
 *             default_root_name?: scalar|null|Param,
 *             default_root_ns?: scalar|null|Param, // Default: ""
 *         },
 *         xml_deserialization?: array{
 *             doctype_whitelist?: list<scalar|null|Param>,
 *             external_entities?: bool|Param, // Default: false
 *             options?: scalar|null|Param, // Default: 0
 *         },
 *     },
 *     default_context?: array{
 *         serialization?: string|array{
 *             id?: scalar|null|Param,
 *             serialize_null?: scalar|null|Param, // Flag if null values should be serialized
 *             enable_max_depth_checks?: scalar|null|Param, // Flag to enable the max-depth exclusion strategy
 *             attributes?: array<string, scalar|null|Param>,
 *             groups?: list<scalar|null|Param>,
 *             version?: scalar|null|Param, // Application version to use in exclusion strategies
 *         },
 *         deserialization?: string|array{
 *             id?: scalar|null|Param,
 *             serialize_null?: scalar|null|Param, // Flag if null values should be serialized
 *             enable_max_depth_checks?: scalar|null|Param, // Flag to enable the max-depth exclusion strategy
 *             attributes?: array<string, scalar|null|Param>,
 *             groups?: list<scalar|null|Param>,
 *             version?: scalar|null|Param, // Application version to use in exclusion strategies
 *         },
 *     },
 *     instances?: array<string, array{ // Default: []
 *         inherit?: bool|Param, // Default: false
 *         enum_support?: scalar|null|Param, // Default: false
 *         default_value_property_reader_support?: scalar|null|Param, // Default: false
 *         handlers?: array{
 *             datetime?: array{
 *                 default_format?: scalar|null|Param, // Default: "Y-m-d\\TH:i:sP"
 *                 default_deserialization_formats?: list<scalar|null|Param>,
 *                 default_timezone?: scalar|null|Param, // Default: "Europe/Berlin"
 *                 cdata?: scalar|null|Param, // Default: true
 *             },
 *             array_collection?: array{
 *                 initialize_excluded?: bool|Param, // Default: false
 *             },
 *             symfony_uid?: array{
 *                 default_format?: scalar|null|Param, // Default: "canonical"
 *                 cdata?: scalar|null|Param, // Default: true
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
 *             id?: scalar|null|Param,
 *             separator?: scalar|null|Param, // Default: "_"
 *             lower_case?: bool|Param, // Default: true
 *         },
 *         expression_evaluator?: string|array{
 *             id?: scalar|null|Param, // Default: "jms_serializer.expression_evaluator"
 *         },
 *         metadata?: array{
 *             warmup?: array{
 *                 paths?: array{
 *                     included?: list<scalar|null|Param>,
 *                     excluded?: list<scalar|null|Param>,
 *                 },
 *             },
 *             cache?: scalar|null|Param, // Default: "file"
 *             debug?: bool|Param, // Default: true
 *             file_cache?: array{
 *                 dir?: scalar|null|Param, // Default: null
 *             },
 *             include_interfaces?: bool|Param, // Default: false
 *             auto_detection?: bool|Param, // Default: true
 *             infer_types_from_doc_block?: bool|Param, // Default: false
 *             infer_types_from_doctrine_metadata?: bool|Param, // Infers type information from Doctrine metadata if no explicit type has been defined for a property. // Default: true
 *             directories?: array<string, array{ // Default: []
 *                 path: scalar|null|Param,
 *                 namespace_prefix?: scalar|null|Param, // Default: ""
 *             }>,
 *         },
 *         visitors?: array{
 *             json_serialization?: array{
 *                 depth?: scalar|null|Param,
 *                 options?: scalar|null|Param, // Default: 1024
 *             },
 *             json_deserialization?: array{
 *                 options?: scalar|null|Param, // Default: 0
 *                 strict?: bool|Param, // Default: false
 *             },
 *             xml_serialization?: array{
 *                 version?: scalar|null|Param,
 *                 encoding?: scalar|null|Param,
 *                 format_output?: bool|Param, // Default: false
 *                 default_root_name?: scalar|null|Param,
 *                 default_root_ns?: scalar|null|Param, // Default: ""
 *             },
 *             xml_deserialization?: array{
 *                 doctype_whitelist?: list<scalar|null|Param>,
 *                 external_entities?: bool|Param, // Default: false
 *                 options?: scalar|null|Param, // Default: 0
 *             },
 *         },
 *         default_context?: array{
 *             serialization?: string|array{
 *                 id?: scalar|null|Param,
 *                 serialize_null?: scalar|null|Param, // Flag if null values should be serialized
 *                 enable_max_depth_checks?: scalar|null|Param, // Flag to enable the max-depth exclusion strategy
 *                 attributes?: array<string, scalar|null|Param>,
 *                 groups?: list<scalar|null|Param>,
 *                 version?: scalar|null|Param, // Application version to use in exclusion strategies
 *             },
 *             deserialization?: string|array{
 *                 id?: scalar|null|Param,
 *                 serialize_null?: scalar|null|Param, // Flag if null values should be serialized
 *                 enable_max_depth_checks?: scalar|null|Param, // Flag to enable the max-depth exclusion strategy
 *                 attributes?: array<string, scalar|null|Param>,
 *                 groups?: list<scalar|null|Param>,
 *                 version?: scalar|null|Param, // Application version to use in exclusion strategies
 *             },
 *         },
 *     }>,
 * }
 * @psalm-type CoreShopPimcoreConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 * }
 * @psalm-type CoreShopLocaleConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 * }
 * @psalm-type CoreShopResourceConfig = array{
 *     mapping?: array{
 *         paths?: list<scalar|null|Param>,
 *     },
 *     resources?: array<string, array{ // Default: []
 *         driver?: scalar|null|Param, // Default: "doctrine/orm"
 *         options?: mixed,
 *         templates?: scalar|null|Param,
 *         classes: array{
 *             model: scalar|null|Param,
 *             interface?: scalar|null|Param,
 *             admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *             repository?: scalar|null|Param,
 *             factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *         },
 *         translation?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes: array{
 *                 model: scalar|null|Param,
 *                 interface?: scalar|null|Param,
 *                 controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|null|Param,
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     }>,
 *     pimcore?: array<string, array{ // Default: []
 *         options?: mixed,
 *         path?: array<string, scalar|null|Param>,
 *         classes?: array{
 *             model: scalar|null|Param,
 *             pimcore_class_name?: scalar|null|Param,
 *             interface?: scalar|null|Param,
 *             repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Pimcore\\PimcoreRepository"
 *             factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *             install_file?: scalar|null|Param,
 *             type?: scalar|null|Param, // Default: "object"
 *             pimcore_controller?: array<string, scalar|null|Param>,
 *         },
 *     }>,
 *     translation?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         locale_provider?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Translation\\Provider\\TranslationLocaleProviderInterface"
 *     },
 *     drivers?: list<"doctrine/orm"|Param>,
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 *     orm_cascade_merge_associations?: array<string, array{ // Default: []
 *         associations?: list<scalar|null|Param>,
 *     }>,
 * }
 * @psalm-type CoreShopSeoConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 * }
 * @psalm-type CoreShopMoneyConfig = array{
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 * }
 * @psalm-type CoreShopWorkflowConfig = array{
 *     state_machine?: array<string, array{ // Default: []
 *         places: list<scalar|null|Param>,
 *         transitions: array<string, array{ // Default: []
 *             name: scalar|null|Param,
 *             guard?: scalar|null|Param, // An expression to block the transition
 *             from?: list<scalar|null|Param>,
 *             to?: list<scalar|null|Param>,
 *         }>,
 *         place_colors?: array<string, scalar|null|Param>,
 *         transition_colors?: array<string, scalar|null|Param>,
 *         callbacks?: array{
 *             guard?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|null|Param, // Default: 0
 *                 args?: list<scalar|null|Param>,
 *             }>,
 *             before?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|null|Param, // Default: 0
 *                 args?: list<scalar|null|Param>,
 *             }>,
 *             after?: array<string, array{ // Default: []
 *                 enabled?: bool|Param, // Default: true
 *                 on?: mixed,
 *                 do?: mixed,
 *                 priority?: scalar|null|Param, // Default: 0
 *                 args?: list<scalar|null|Param>,
 *             }>,
 *         },
 *     }>,
 * }
 * @psalm-type CoreShopMessengerConfig = array{
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["messenger"]
 *     },
 *     doctrine?: array{
 *         table_name?: scalar|null|Param, // Default: null
 *         connection?: scalar|null|Param, // Default: null
 *     },
 * }
 * @psalm-type CoreShopRuleConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         rule_condition?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Rule\\Model\\Condition"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Rule\\Model\\ConditionInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *             },
 *         },
 *         rule_action?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Rule\\Model\\Action"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Rule\\Model\\ActionInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 * }
 * @psalm-type CoreShopConfigurationConfig = array{
 *     resources?: array{
 *         configuration?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Configuration\\Model\\Configuration"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Configuration\\Model\\ConfigurationInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Controller\\ConfigurationController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Doctrine\\ORM\\ConfigurationRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ConfigurationBundle\\Form\\Type\\ConfigurationType"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopOrderConfig = array{
 *     allow_order_edit?: bool|Param, // Default: false
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         cart_price_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "cart_price_rule"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\CartPriceRuleController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Form\\Type\\CartPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Form\\Type\\CartPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         cart_price_rule_voucher_code?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCode"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleVoucherRepository"
 *             },
 *         },
 *         cart_price_rule_voucher_code_customer?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeCustomer"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\CartPriceRuleVoucherCodeCustomerInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Factory\\CartPriceRuleVoucherCodeCustomerFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Doctrine\\ORM\\CartPriceRuleVoucherCodeCustomerRepository"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         order?: array{
 *             options?: mixed,
 *             path?: array{
 *                 order?: scalar|null|Param, // Default: "orders"
 *                 quote?: scalar|null|Param, // Default: "quotes"
 *                 cart?: scalar|null|Param, // Default: "carts"
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrder"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrder.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *                 pimcore_controller?: array{
 *                     default?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderController"
 *                     creation?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderCreationController"
 *                     edit?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderEditController"
 *                     payment?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderPaymentController"
 *                     comment?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderCommentController"
 *                     customer_creation?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\CustomerCreationController"
 *                     address_creation?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\AddressCreationController"
 *                 },
 *             },
 *         },
 *         order_item?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderItem"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderItemRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItem.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         order_invoice?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "invoices"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderInvoice"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderInvoiceRepository"
 *                 pimcore_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderInvoiceController"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoice.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         order_invoice_item?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderInvoiceItem"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoiceItem.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         order_shipment?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "shipments"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderShipment"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\OrderShipmentRepository"
 *                 pimcore_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\OrderBundle\\Controller\\OrderShipmentController"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipment.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         order_shipment_item?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopOrderShipmentItem"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipmentItem.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         cart_price_rule_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopProposalCartPriceRuleItem"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\ProposalCartPriceRuleItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopProposalCartPriceRuleItem.json"
 *                 type?: scalar|null|Param, // Default: "fieldcollection"
 *             },
 *         },
 *         price_rule_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopPriceRuleItem"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\PriceRuleItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopPriceRuleItem.json"
 *                 type?: scalar|null|Param, // Default: "fieldcollection"
 *             },
 *         },
 *         adjustment?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopAdjustment"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\AdjustmentInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopAdjustment.json"
 *                 type?: scalar|null|Param, // Default: "fieldcollection"
 *             },
 *         },
 *         order_item_attribute?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopOrderItemAttribute"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemAttributeInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopOrderItemAttribute.json"
 *                 type?: scalar|null|Param, // Default: "fieldcollection"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["cart_price_rule","order_list","order_detail","order_create","quote_list","quote_detail","quote_create","cart_list","cart_detail","cart_create"]
 *         install?: array{
 *             grid_config?: list<scalar|null|Param>,
 *         },
 *     },
 *     stack?: array{
 *         purchasable?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\PurchasableInterface"
 *         order?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInterface"
 *         order_item?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderItemInterface"
 *         order_invoice?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceInterface"
 *         order_invoice_item?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderInvoiceItemInterface"
 *         order_shipment?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentInterface"
 *         order_shipment_item?: scalar|null|Param, // Default: "CoreShop\\Component\\Order\\Model\\OrderShipmentItemInterface"
 *     },
 * }
 * @psalm-type CoreShopCustomerConfig = array{
 *     login_identifier?: "email"|"username"|Param, // Default: "email"
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     stack?: array{
 *         customer?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerInterface"
 *         customer_group?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerGroupInterface"
 *         company?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CompanyInterface"
 *     },
 *     pimcore?: array{
 *         company?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "companies"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopCompany"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CompanyInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CustomerBundle\\Pimcore\\Repository\\CompanyRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCompany.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         customer?: array{
 *             options?: mixed,
 *             path?: array{
 *                 customer?: scalar|null|Param, // Default: "customers"
 *                 guest?: scalar|null|Param, // Default: "guests"
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopCustomer"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CustomerBundle\\Pimcore\\Repository\\CustomerRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomer.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         customer_group?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "customer_groups"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopCustomerGroup"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Customer\\Model\\CustomerGroupInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopCustomerBundle/Resources/install/pimcore/classes/CoreShopCustomerGroup.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["customer_list","customer_group_list"]
 *         install?: array{
 *             grid_config?: list<scalar|null|Param>,
 *         },
 *     },
 * }
 * @psalm-type CoreShopUserConfig = array{
 *     driver?: scalar|null|Param, // Default: "doctrine/orm"
 *     stack?: array{
 *         user?: scalar|null|Param, // Default: "CoreShop\\Component\\User\\Model\\UserInterface"
 *     },
 *     pimcore?: array{
 *         user?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "user"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopUser"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\User\\Model\\UserInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\UserBundle\\Pimcore\\Repository\\UserRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopUserBundle/Resources/install/pimcore/classes/CoreShopUser.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 * }
 * @psalm-type CoreShopInventoryConfig = array{
 *     checker?: scalar|null|Param, // Default: "CoreShop\\Component\\Inventory\\Checker\\AvailabilityChecker"
 * }
 * @psalm-type CoreShopVariantConfig = array{
 *     stack?: array{
 *         attribute_group?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeGroupInterface"
 *         attribute?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeInterface"
 *         variant_aware?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\ProductVariantAwareInterface"
 *     },
 *     pimcore?: array{
 *         attribute_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeGroup"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeGroupInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeGroup.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         attribute_value?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeValue"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeValueInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeValue.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         attribute_color?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopAttributeColor"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Variant\\Model\\AttributeColorInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopVariantBundle/Resources/install/pimcore/classes/CoreShopAttributeColor.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 *     redirect_to_main_variant?: scalar|null|Param, // Default: true
 * }
 * @psalm-type CoreShopProductConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         product_price_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "product_price_rule"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Controller\\ProductPriceRuleController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductPriceRuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductPriceRuleTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         product_specific_price_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductSpecificPriceRuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductSpecificPriceRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductSpecificPriceRuleTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\ProductSpecificPriceRuleTranslationType"
 *                 },
 *             },
 *         },
 *         product_unit?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "product_unit"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnit"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductUnitRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\Unit\\ProductUnitType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Form\\Type\\Unit\\ProductUnitTranslationType"
 *                 },
 *             },
 *         },
 *         product_unit_definitions?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitions"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionsInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Controller\\ProductUnitDefinitionsController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Doctrine\\ORM\\ProductUnitDefinitionsRepository"
 *             },
 *         },
 *         product_unit_definition?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinition"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *         product_unit_definition_price?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionPrice"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductUnitDefinitionPriceInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         product?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "products"
 *             slug?: bool|Param, // Default: true
 *             route?: array{
 *                 name?: scalar|null|Param, // Default: "coreshop_product_detail"
 *                 id_param?: scalar|null|Param, // Default: "product"
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopProduct"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Pimcore\\Repository\\ProductRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopProduct.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         category?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "categories"
 *             slug?: bool|Param, // Default: true
 *             route?: array{
 *                 name?: scalar|null|Param, // Default: "coreshop_category_list"
 *                 id_param?: scalar|null|Param, // Default: "category"
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopCategory"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\CategoryInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductBundle\\Pimcore\\Repository\\CategoryRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopCategory.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         manufacturer?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "manufacturers"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopManufacturer"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ManufacturerInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopManufacturer.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["product_price_rule","product_unit"]
 *     },
 *     stack?: array{
 *         product?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ProductInterface"
 *         category?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\CategoryInterface"
 *         manufacturer?: scalar|null|Param, // Default: "CoreShop\\Component\\Product\\Model\\ManufacturerInterface"
 *     },
 * }
 * @psalm-type CoreShopThemeConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     default_resolvers?: array{
 *         pimcore_site?: bool|Param, // Default: false
 *         pimcore_document_property?: bool|Param, // Default: false
 *     },
 * }
 * @psalm-type CoreShopAddressConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     stack?: array{
 *         address?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\AddressInterface"
 *     },
 *     resources?: array{
 *         country?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "country"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\Country"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\CountryInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Controller\\CountryController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Doctrine\\ORM\\CountryRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\CountryType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\CountryTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\CountryTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\CountryTranslationType"
 *                 },
 *             },
 *         },
 *         zone?: array{
 *             permission?: scalar|null|Param, // Default: "zone"
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\Zone"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\ZoneInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\ZoneType"
 *             },
 *         },
 *         state?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "state"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\State"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\StateInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\StateType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\StateTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\StateTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\StateTranslationType"
 *                 },
 *             },
 *         },
 *         address_identifier?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "address_identifier"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\AddressIdentifier"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\AddressIdentifierInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Doctrine\\ORM\\AddressIdentifierRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\AddressBundle\\Form\\Type\\AddressIdentifierType"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         address?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "addresses"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopAddress"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Address\\Model\\AddressInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopAddressBundle/Resources/install/pimcore/classes/CoreShopAddress.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["country","state","zone"]
 *     },
 * }
 * @psalm-type CoreShopCurrencyConfig = array{
 *     money_decimal_factor?: int|Param, // Default: 100
 *     money_decimal_precision?: int|Param, // Default: 2
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         currency?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             permission?: scalar|null|Param, // Default: "currency"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Currency\\Model\\Currency"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Currency\\Model\\CurrencyInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Controller\\CurrencyController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Doctrine\\ORM\\CurrencyRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Form\\Type\\CurrencyType"
 *             },
 *         },
 *         exchange_rate?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "exchange_rate"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Currency\\Model\\ExchangeRate"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Currency\\Model\\ExchangeRateInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Controller\\ExchangeRateController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Doctrine\\ORM\\ExchangeRateRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CurrencyBundle\\Form\\Type\\ExchangeRateType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["currency","exchange_rate"]
 *     },
 * }
 * @psalm-type CoreShopTaxationConfig = array{
 *     resources?: array{
 *         tax_rate?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "tax_rate"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRate"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Doctrine\\ORM\\TaxRateRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRateType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRateTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRateTranslationType"
 *                 },
 *             },
 *         },
 *         tax_rule_group?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "tax_rule_group"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleGroup"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleGroupInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Controller\\TaxRuleGroupController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRuleGroupType"
 *             },
 *         },
 *         tax_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxRuleInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Doctrine\\ORM\\TaxRuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\TaxationBundle\\Form\\Type\\TaxRuleType"
 *             },
 *         },
 *     },
 *     pimcore?: array{
 *         tax_item?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\CoreShopTaxItem"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Taxation\\Model\\TaxItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param,
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopTaxationBundle/Resources/install/pimcore/fieldcollections/CoreShopTaxItem.json"
 *                 type?: scalar|null|Param, // Default: "fieldcollection"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["tax_rate","tax_rule_group"]
 *     },
 * }
 * @psalm-type CoreShopStoreConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         store?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "store"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Store\\Model\\Store"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Store\\Model\\StoreInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\StoreBundle\\Controller\\StoreController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\StoreBundle\\Doctrine\\ORM\\StoreRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\StoreBundle\\Form\\Type\\StoreType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["store"]
 *     },
 * }
 * @psalm-type CoreShopIndexConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     mysql_auto_generate_migrations?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         index?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "index"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\Index"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\IndexInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Controller\\IndexController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\IndexType"
 *             },
 *         },
 *         index_column?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\IndexColumn"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\IndexColumnInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\IndexColumnType"
 *             },
 *         },
 *         filter?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "filter"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\Filter"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\FilterInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Controller\\FilterController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\FilterType"
 *             },
 *         },
 *         filter_condition?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\FilterCondition"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Index\\Model\\FilterConditionInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\IndexBundle\\Form\\Type\\FilterConditionType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["index","filter"]
 *     },
 *     mapping_types?: array<string, scalar|null|Param>,
 *     worker_mapping_types?: array<string, array<string, scalar|null|Param>>,
 * }
 * @psalm-type CoreShopShippingConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     default_resolver?: scalar|null|Param,
 *     resources?: array{
 *         carrier?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "carrier"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\Carrier"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Controller\\CarrierController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\CarrierType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\CarrierTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\CarrierTranslationType"
 *                 },
 *             },
 *         },
 *         shipping_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "shipping_rule"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Controller\\ShippingRuleController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\RuleBundle\\Doctrine\\ORM\\RuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\ShippingRuleType"
 *             },
 *         },
 *         shipping_rule_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleGroup"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Shipping\\Model\\ShippingRuleGroupInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ShippingBundle\\Form\\Type\\ShippingRuleGroupType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["carrier","shipping_rule"]
 *     },
 * }
 * @psalm-type CoreShopPaymentConfig = array{
 *     driver?: scalar|null|Param, // Default: "doctrine/orm"
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         payment_provider?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "payment_provider"
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProvider"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\TranslatableFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Doctrine\\ORM\\PaymentProviderRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderTranslationType"
 *                 },
 *             },
 *         },
 *         payment?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\Payment"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Doctrine\\ORM\\PaymentRepository"
 *             },
 *         },
 *         payment_provider_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "payment_provider_rule"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Controller\\PaymentProviderRuleController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\RuleBundle\\Doctrine\\ORM\\RuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleType"
 *             },
 *             translation?: array{
 *                 options?: mixed,
 *                 graphql?: array{
 *                     enabled?: bool|Param, // Default: true
 *                 },
 *                 classes?: array{
 *                     model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleTranslation"
 *                     interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleTranslationInterface"
 *                     repository?: scalar|null|Param,
 *                     factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                     form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleTranslationType"
 *                 },
 *             },
 *         },
 *         payment_provider_rule_group?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleGroup"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Payment\\Model\\PaymentProviderRuleGroupInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PaymentBundle\\Form\\Type\\PaymentProviderRuleGroupType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["payment_provider","payment_provider_rule"]
 *     },
 * }
 * @psalm-type CoreShopAiConfig = array{
 *     platform?: scalar|null|Param, // Service ID of the symfony/ai PlatformInterface to use (e.g. ai.platform.anthropic) // Default: null
 *     model?: scalar|null|Param, // Model identifier to use for LLM calls // Default: "claude-sonnet-4-20250514"
 *     approval?: array{
 *         file_write?: bool|Param, // Require approval for file write operations // Default: true
 *         file_delete?: bool|Param, // Require approval for file delete operations // Default: true
 *         db_write?: bool|Param, // Require approval for database write operations (INSERT/UPDATE/DELETE) // Default: true
 *         command_execute?: bool|Param, // Require approval for command execution (if not whitelisted) // Default: true
 *         command_whitelist?: list<scalar|null|Param>,
 *     },
 *     safety?: array{
 *         allowed_paths?: list<scalar|null|Param>,
 *         forbidden_patterns?: list<scalar|null|Param>,
 *     },
 *     context?: array{
 *         compaction_threshold?: int|Param, // Number of exchanges before context compaction // Default: 10
 *         token_budget_threshold?: float|Param, // Token budget percentage threshold for compaction (0.0 - 1.0) // Default: 0.8
 *         max_tokens?: int|Param, // Maximum context window tokens before compaction is triggered // Default: 150000
 *     },
 *     token_budgets?: array{
 *         per_conversation?: array{
 *             max_total_tokens?: int|Param, // Maximum total tokens per conversation (input + output) // Default: 600000
 *             max_cost_usd?: float|Param, // Maximum cost in USD per conversation // Default: 5.0
 *             max_iterations?: int|Param, // Maximum LLM iterations per conversation // Default: 50
 *         },
 *         per_session?: array{
 *             max_total_tokens?: int|Param, // Maximum total tokens per session (null = unlimited) // Default: null
 *             max_cost_usd?: float|Param, // Maximum cost in USD per session (null = unlimited) // Default: null
 *         },
 *     },
 *     subagent?: array{
 *         enabled?: bool|Param, // Enable SubAgent system for parallel task execution // Default: true
 *         max_concurrent?: int|Param, // Maximum concurrent SubAgents per parent session // Default: 5
 *         default_timeout?: int|Param, // Default timeout in seconds for SubAgent execution // Default: 300
 *         max_depth?: int|Param, // Maximum nesting depth for SubAgents // Default: 3
 *     },
 * }
 * @psalm-type CoreShopSequenceConfig = array{
 *     resources?: array{
 *         sequence?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Sequence\\Model\\Sequence"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Sequence\\Model\\SequenceInterface"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Sequence\\Factory\\SequenceFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\SequenceBundle\\Doctrine\\ORM\\SequenceRepository"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopPayumPaymentConfig = array{
 *     driver?: scalar|null|Param, // Default: "doctrine/orm"
 *     resources?: array{
 *         gateway_config?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\PayumPayment\\Model\\GatewayConfig"
 *                 controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *         payment_security_token?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\PayumPayment\\Model\\PaymentSecurityToken"
 *                 controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ResourceBundle\\Controller\\ResourceController"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *             },
 *         },
 *     },
 * }
 * @psalm-type CoreShopNotificationConfig = array{
 *     resources?: array{
 *         notification_rule?: array{
 *             options?: mixed,
 *             permission?: scalar|null|Param, // Default: "notification"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Notification\\Model\\NotificationRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Notification\\Model\\NotificationRuleInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\NotificationBundle\\Doctrine\\ORM\\NotificationRuleRepository"
 *                 admin_controller?: scalar|null|Param, // Default: "CoreShop\\Bundle\\NotificationBundle\\Controller\\NotificationRuleController"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\NotificationBundle\\Form\\Type\\NotificationRuleType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["notification"]
 *     },
 * }
 * @psalm-type CoreShopTrackingConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     trackers?: array<string, array{ // Default: []
 *         enabled?: bool|Param, // Default: false
 *     }>,
 * }
 * @psalm-type CoreShopFrontendConfig = array{
 *     view_suffix?: scalar|null|Param, // Default: "twig"
 *     view_bundle?: scalar|null|Param, // Deprecated: Use view_prefix instead // Default: "@CoreShopFrontend"
 *     view_prefix?: scalar|null|Param, // Default: "@CoreShopFrontend"
 *     category?: array{
 *         valid_sort_options?: list<scalar|null|Param>,
 *         default_sort_name?: scalar|null|Param, // Default: "name"
 *         default_sort_direction?: scalar|null|Param, // Default: "asc"
 *     },
 *     pimcore_admin?: array{
 *         install?: array{
 *             routes?: list<scalar|null|Param>,
 *             documents?: list<scalar|null|Param>,
 *             image_thumbnails?: list<scalar|null|Param>,
 *         },
 *     },
 *     controllers?: array{
 *         index?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\IndexController"
 *         register?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\RegisterController"
 *         customer?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CustomerController"
 *         currency?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CurrencyController"
 *         search?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\SearchController"
 *         cart?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CartController"
 *         checkout?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CheckoutController"
 *         order?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\OrderController"
 *         category?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\CategoryController"
 *         product?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\ProductController"
 *         quote?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\QuoteController"
 *         security?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\SecurityController"
 *         payment?: scalar|null|Param, // Default: "CoreShop\\Bundle\\PayumBundle\\Controller\\PaymentController"
 *         mail?: scalar|null|Param, // Default: "CoreShop\\Bundle\\FrontendBundle\\Controller\\MailController"
 *     },
 * }
 * @psalm-type CoreShopPayumConfig = array{
 *     template?: array{
 *         layout?: scalar|null|Param, // Default: "@CoreShopPayum/:layout.html.twig"
 *         obtain_credit_card?: scalar|null|Param, // Default: "@CoreShopPayum/Action/obtainCreditCard.html.twig"
 *     },
 * }
 * @psalm-type CoreShopProductQuantityPriceRulesConfig = array{
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     action_constraints?: list<array{ // Default: []
 *         class?: scalar|null|Param,
 *         groups?: array<string, scalar|null|Param>,
 *     }>,
 *     resources?: array{
 *         product_quantity_price_rule_range?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\QuantityRange"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\QuantityRangeInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param,
 *             },
 *         },
 *         product_quantity_price_rule?: array{
 *             options?: mixed,
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\ProductQuantityPriceRule"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\ProductQuantityPriceRules\\Model\\ProductQuantityPriceRuleInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductQuantityPriceRulesBundle\\Doctrine\\ORM\\ProductQuantityPriceRuleRepository"
 *                 form?: scalar|null|Param, // Default: "CoreShop\\Bundle\\ProductQuantityPriceRulesBundle\\Form\\Type\\ProductQuantityPriceRuleType"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array<string, scalar|null|Param>,
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *         permissions?: scalar|null|Param, // Default: ["notification"]
 *     },
 * }
 * @psalm-type CoreShopWishlistConfig = array{
 *     pimcore?: array{
 *         wishlist?: array{
 *             options?: mixed,
 *             path?: array{
 *                 wishlist?: scalar|null|Param, // Default: "wishlists"
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopWishlist"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\WishlistBundle\\Pimcore\\Repository\\WishlistRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlist.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *         wishlist_item?: array{
 *             options?: mixed,
 *             path?: scalar|null|Param, // Default: "items"
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "Pimcore\\Model\\DataObject\\CoreShopWishlistItem"
 *                 pimcore_class_name?: scalar|null|Param,
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistItemInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\PimcoreFactory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\WishlistBundle\\Pimcore\\Repository\\WishlistItemRepository"
 *                 install_file?: scalar|null|Param, // Default: "@CoreShopWishlistBundle/Resources/install/pimcore/classes/CoreShopWishlistItem.json"
 *                 type?: scalar|null|Param, // Default: "object"
 *             },
 *         },
 *     },
 *     stack?: array{
 *         wishlist?: scalar|null|Param, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistInterface"
 *         wishlist_item?: scalar|null|Param, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistItemInterface"
 *         wishlist_product?: scalar|null|Param, // Default: "CoreShop\\Component\\Wishlist\\Model\\WishlistProductInterface"
 *     },
 * }
 * @psalm-type CoreShopClassDefinitionPatchConfig = array{
 *     patches?: array<string, array{ // Default: []
 *         interface?: list<scalar|null|Param>,
 *         parent_class?: scalar|null|Param,
 *         group?: scalar|null|Param,
 *         description?: scalar|null|Param,
 *         listing_parent_class?: scalar|null|Param,
 *         use_traits?: list<scalar|null|Param>,
 *         listing_use_traits?: list<scalar|null|Param>,
 *         fields?: array<string, array{ // Default: []
 *             after?: scalar|null|Param,
 *             before?: scalar|null|Param,
 *             replace?: bool|Param, // Default: true
 *             definition?: mixed,
 *         }>,
 *     }>,
 * }
 * @psalm-type PayumConfig = array{
 *     security: array{
 *         token_storage: array<string, array{ // Default: []
 *             filesystem?: array{
 *                 storage_dir: scalar|null|Param,
 *                 id_property?: scalar|null|Param, // Default: null
 *             },
 *             doctrine?: string|array{
 *                 driver: scalar|null|Param,
 *             },
 *             custom?: string|array{
 *                 service: scalar|null|Param,
 *             },
 *             propel1?: array<mixed>,
 *             propel2?: array<mixed>,
 *         }>,
 *     },
 *     dynamic_gateways?: array{
 *         sonata_admin?: bool|Param, // Default: false
 *         config_storage: array<string, array{ // Default: []
 *             filesystem?: array{
 *                 storage_dir: scalar|null|Param,
 *                 id_property?: scalar|null|Param, // Default: null
 *             },
 *             doctrine?: string|array{
 *                 driver: scalar|null|Param,
 *             },
 *             custom?: string|array{
 *                 service: scalar|null|Param,
 *             },
 *             propel1?: array<mixed>,
 *             propel2?: array<mixed>,
 *         }>,
 *         encryption?: array{
 *             defuse_secret_key?: scalar|null|Param,
 *         },
 *     },
 *     gateways?: array<string, mixed>,
 *     storages?: array<string, array{ // Default: []
 *         extension?: array{
 *             all?: bool|Param, // Default: true
 *             gateways?: array<string, scalar|null|Param>,
 *             factories?: array<string, scalar|null|Param>,
 *         },
 *         filesystem?: array{
 *             storage_dir: scalar|null|Param,
 *             id_property?: scalar|null|Param, // Default: null
 *         },
 *         doctrine?: string|array{
 *             driver: scalar|null|Param,
 *         },
 *         custom?: string|array{
 *             service: scalar|null|Param,
 *         },
 *         propel1?: array<mixed>,
 *         propel2?: array<mixed>,
 *     }>,
 * }
 * @psalm-type StofDoctrineExtensionsConfig = array{
 *     orm?: array<string, array{ // Default: []
 *         translatable?: scalar|null|Param, // Default: false
 *         timestampable?: scalar|null|Param, // Default: false
 *         blameable?: scalar|null|Param, // Default: false
 *         sluggable?: scalar|null|Param, // Default: false
 *         tree?: scalar|null|Param, // Default: false
 *         loggable?: scalar|null|Param, // Default: false
 *         ip_traceable?: scalar|null|Param, // Default: false
 *         sortable?: scalar|null|Param, // Default: false
 *         softdeleteable?: scalar|null|Param, // Default: false
 *         uploadable?: scalar|null|Param, // Default: false
 *         reference_integrity?: scalar|null|Param, // Default: false
 *     }>,
 *     mongodb?: array<string, array{ // Default: []
 *         translatable?: scalar|null|Param, // Default: false
 *         timestampable?: scalar|null|Param, // Default: false
 *         blameable?: scalar|null|Param, // Default: false
 *         sluggable?: scalar|null|Param, // Default: false
 *         tree?: scalar|null|Param, // Default: false
 *         loggable?: scalar|null|Param, // Default: false
 *         ip_traceable?: scalar|null|Param, // Default: false
 *         sortable?: scalar|null|Param, // Default: false
 *         softdeleteable?: scalar|null|Param, // Default: false
 *         uploadable?: scalar|null|Param, // Default: false
 *         reference_integrity?: scalar|null|Param, // Default: false
 *     }>,
 *     class?: array{
 *         translatable?: scalar|null|Param, // Default: "Gedmo\\Translatable\\TranslatableListener"
 *         timestampable?: scalar|null|Param, // Default: "Gedmo\\Timestampable\\TimestampableListener"
 *         blameable?: scalar|null|Param, // Default: "Gedmo\\Blameable\\BlameableListener"
 *         sluggable?: scalar|null|Param, // Default: "Gedmo\\Sluggable\\SluggableListener"
 *         tree?: scalar|null|Param, // Default: "Gedmo\\Tree\\TreeListener"
 *         loggable?: scalar|null|Param, // Default: "Gedmo\\Loggable\\LoggableListener"
 *         sortable?: scalar|null|Param, // Default: "Gedmo\\Sortable\\SortableListener"
 *         softdeleteable?: scalar|null|Param, // Default: "Gedmo\\SoftDeleteable\\SoftDeleteableListener"
 *         uploadable?: scalar|null|Param, // Default: "Gedmo\\Uploadable\\UploadableListener"
 *         reference_integrity?: scalar|null|Param, // Default: "Gedmo\\ReferenceIntegrity\\ReferenceIntegrityListener"
 *     },
 *     softdeleteable?: array{
 *         handle_post_flush_event?: bool|Param, // Default: false
 *     },
 *     uploadable?: array{
 *         default_file_path?: scalar|null|Param, // Default: null
 *         mime_type_guesser_class?: scalar|null|Param, // Default: "Stof\\DoctrineExtensionsBundle\\Uploadable\\MimeTypeGuesserAdapter"
 *         default_file_info_class?: scalar|null|Param, // Default: "Stof\\DoctrineExtensionsBundle\\Uploadable\\UploadedFileInfo"
 *         validate_writable_directory?: bool|Param, // Default: true
 *     },
 *     default_locale?: scalar|null|Param, // Default: "en"
 *     translation_fallback?: bool|Param, // Default: false
 *     persist_default_translation?: bool|Param, // Default: false
 *     skip_translation_on_load?: bool|Param, // Default: false
 *     metadata_cache_pool?: scalar|null|Param, // Default: null
 * }
 * @psalm-type SyliusThemeConfig = array{
 *     sources?: array{
 *         filesystem?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             filename?: scalar|null|Param, // Default: "composer.json"
 *             scan_depth?: scalar|null|Param, // Restrict depth to scan for configuration file inside theme folder // Default: 1
 *             directories?: list<scalar|null|Param>,
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
 *     context?: scalar|null|Param, // Default: "sylius.theme.context.settable"
 *     legacy_mode?: bool|Param, // Deprecated: "legacy_mode" at path "sylius_theme.legacy_mode" is deprecated since Sylius/ThemeBundle 2.0 and will be removed in 3.0. // Default: false
 * }
 * @psalm-type PimcoreGoogleMarketingConfig = array{
 *     client_id?: scalar|null|Param, // This is required for the Google API integrations. Only use a `Service Account´ from the Google Cloud Console. // Default: null
 *     email?: scalar|null|Param, // Email address of the Google service account // Default: null
 *     simple_api_key?: scalar|null|Param, // Server API key // Default: null
 *     browser_api_key?: scalar|null|Param, // Browser API key // Default: null
 * }
 * @psalm-type FrameworkConfig = array{
 *     secret?: scalar|null|Param,
 *     http_method_override?: bool|Param, // Set true to enable support for the '_method' request parameter to determine the intended HTTP method on POST requests. // Default: false
 *     allowed_http_method_override?: list<string|Param>|null,
 *     trust_x_sendfile_type_header?: scalar|null|Param, // Set true to enable support for xsendfile in binary file responses. // Default: "%env(bool:default::SYMFONY_TRUST_X_SENDFILE_TYPE_HEADER)%"
 *     ide?: scalar|null|Param, // Default: "%env(default::SYMFONY_IDE)%"
 *     test?: bool|Param,
 *     default_locale?: scalar|null|Param, // Default: "en"
 *     set_locale_from_accept_language?: bool|Param, // Whether to use the Accept-Language HTTP header to set the Request locale (only when the "_locale" request attribute is not passed). // Default: false
 *     set_content_language_from_locale?: bool|Param, // Whether to set the Content-Language HTTP header on the Response using the Request locale. // Default: false
 *     enabled_locales?: list<scalar|null|Param>,
 *     trusted_hosts?: list<scalar|null|Param>,
 *     trusted_proxies?: mixed, // Default: ["%env(default::SYMFONY_TRUSTED_PROXIES)%"]
 *     trusted_headers?: list<scalar|null|Param>,
 *     error_controller?: scalar|null|Param, // Default: "error_controller"
 *     handle_all_throwables?: bool|Param, // HttpKernel will handle all kinds of \Throwable. // Default: true
 *     csrf_protection?: bool|array{
 *         enabled?: scalar|null|Param, // Default: null
 *         stateless_token_ids?: list<scalar|null|Param>,
 *         check_header?: scalar|null|Param, // Whether to check the CSRF token in a header in addition to a cookie when using stateless protection. // Default: false
 *         cookie_name?: scalar|null|Param, // The name of the cookie to use when using stateless protection. // Default: "csrf-token"
 *     },
 *     form?: bool|array{ // Form configuration
 *         enabled?: bool|Param, // Default: true
 *         csrf_protection?: array{
 *             enabled?: scalar|null|Param, // Default: null
 *             token_id?: scalar|null|Param, // Default: null
 *             field_name?: scalar|null|Param, // Default: "_token"
 *             field_attr?: array<string, scalar|null|Param>,
 *         },
 *     },
 *     http_cache?: bool|array{ // HTTP cache configuration
 *         enabled?: bool|Param, // Default: false
 *         debug?: bool|Param, // Default: "%kernel.debug%"
 *         trace_level?: "none"|"short"|"full"|Param,
 *         trace_header?: scalar|null|Param,
 *         default_ttl?: int|Param,
 *         private_headers?: list<scalar|null|Param>,
 *         skip_response_headers?: list<scalar|null|Param>,
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
 *         hinclude_default_template?: scalar|null|Param, // Default: null
 *         path?: scalar|null|Param, // Default: "/_fragment"
 *     },
 *     profiler?: bool|array{ // Profiler configuration
 *         enabled?: bool|Param, // Default: false
 *         collect?: bool|Param, // Default: true
 *         collect_parameter?: scalar|null|Param, // The name of the parameter to use to enable or disable collection on a per request basis. // Default: null
 *         only_exceptions?: bool|Param, // Default: false
 *         only_main_requests?: bool|Param, // Default: false
 *         dsn?: scalar|null|Param, // Default: "file:%kernel.cache_dir%/profiler"
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
 *                 property?: scalar|null|Param,
 *                 service?: scalar|null|Param,
 *             },
 *             supports?: list<scalar|null|Param>,
 *             definition_validators?: list<scalar|null|Param>,
 *             support_strategy?: scalar|null|Param,
 *             initial_marking?: list<scalar|null|Param>,
 *             events_to_dispatch?: list<string|Param>|null,
 *             places?: list<array{ // Default: []
 *                 name: scalar|null|Param,
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
 *         resource: scalar|null|Param,
 *         type?: scalar|null|Param,
 *         cache_dir?: scalar|null|Param, // Deprecated: Setting the "framework.router.cache_dir.cache_dir" configuration option is deprecated. It will be removed in version 8.0. // Default: "%kernel.build_dir%"
 *         default_uri?: scalar|null|Param, // The default URI used to generate URLs in a non-HTTP context. // Default: null
 *         http_port?: scalar|null|Param, // Default: 80
 *         https_port?: scalar|null|Param, // Default: 443
 *         strict_requirements?: scalar|null|Param, // set to true to throw an exception when a parameter does not match the requirements set to false to disable exceptions when a parameter does not match the requirements (and return null instead) set to null to disable parameter checks against requirements 'true' is the preferred configuration in development mode, while 'false' or 'null' might be preferred in production // Default: true
 *         utf8?: bool|Param, // Default: true
 *     },
 *     session?: bool|array{ // Session configuration
 *         enabled?: bool|Param, // Default: false
 *         storage_factory_id?: scalar|null|Param, // Default: "session.storage.factory.native"
 *         handler_id?: scalar|null|Param, // Defaults to using the native session handler, or to the native *file* session handler if "save_path" is not null.
 *         name?: scalar|null|Param,
 *         cookie_lifetime?: scalar|null|Param,
 *         cookie_path?: scalar|null|Param,
 *         cookie_domain?: scalar|null|Param,
 *         cookie_secure?: true|false|"auto"|Param, // Default: "auto"
 *         cookie_httponly?: bool|Param, // Default: true
 *         cookie_samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *         use_cookies?: bool|Param,
 *         gc_divisor?: scalar|null|Param,
 *         gc_probability?: scalar|null|Param,
 *         gc_maxlifetime?: scalar|null|Param,
 *         save_path?: scalar|null|Param, // Defaults to "%kernel.cache_dir%/sessions" if the "handler_id" option is not null.
 *         metadata_update_threshold?: int|Param, // Seconds to wait between 2 session metadata updates. // Default: 0
 *         sid_length?: int|Param, // Deprecated: Setting the "framework.session.sid_length.sid_length" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *         sid_bits_per_character?: int|Param, // Deprecated: Setting the "framework.session.sid_bits_per_character.sid_bits_per_character" configuration option is deprecated. It will be removed in version 8.0. No alternative is provided as PHP 8.4 has deprecated the related option.
 *     },
 *     request?: bool|array{ // Request configuration
 *         enabled?: bool|Param, // Default: false
 *         formats?: array<string, string|list<scalar|null|Param>>,
 *     },
 *     assets?: bool|array{ // Assets configuration
 *         enabled?: bool|Param, // Default: true
 *         strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *         version_strategy?: scalar|null|Param, // Default: null
 *         version?: scalar|null|Param, // Default: null
 *         version_format?: scalar|null|Param, // Default: "%%s?%%s"
 *         json_manifest_path?: scalar|null|Param, // Default: null
 *         base_path?: scalar|null|Param, // Default: ""
 *         base_urls?: list<scalar|null|Param>,
 *         packages?: array<string, array{ // Default: []
 *             strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *             version_strategy?: scalar|null|Param, // Default: null
 *             version?: scalar|null|Param,
 *             version_format?: scalar|null|Param, // Default: null
 *             json_manifest_path?: scalar|null|Param, // Default: null
 *             base_path?: scalar|null|Param, // Default: ""
 *             base_urls?: list<scalar|null|Param>,
 *         }>,
 *     },
 *     asset_mapper?: bool|array{ // Asset Mapper configuration
 *         enabled?: bool|Param, // Default: false
 *         paths?: array<string, scalar|null|Param>,
 *         excluded_patterns?: list<scalar|null|Param>,
 *         exclude_dotfiles?: bool|Param, // If true, any files starting with "." will be excluded from the asset mapper. // Default: true
 *         server?: bool|Param, // If true, a "dev server" will return the assets from the public directory (true in "debug" mode only by default). // Default: true
 *         public_prefix?: scalar|null|Param, // The public path where the assets will be written to (and served from when "server" is true). // Default: "/assets/"
 *         missing_import_mode?: "strict"|"warn"|"ignore"|Param, // Behavior if an asset cannot be found when imported from JavaScript or CSS files - e.g. "import './non-existent.js'". "strict" means an exception is thrown, "warn" means a warning is logged, "ignore" means the import is left as-is. // Default: "warn"
 *         extensions?: array<string, scalar|null|Param>,
 *         importmap_path?: scalar|null|Param, // The path of the importmap.php file. // Default: "%kernel.project_dir%/importmap.php"
 *         importmap_polyfill?: scalar|null|Param, // The importmap name that will be used to load the polyfill. Set to false to disable. // Default: "es-module-shims"
 *         importmap_script_attributes?: array<string, scalar|null|Param>,
 *         vendor_dir?: scalar|null|Param, // The directory to store JavaScript vendors. // Default: "%kernel.project_dir%/assets/vendor"
 *         precompress?: bool|array{ // Precompress assets with Brotli, Zstandard and gzip.
 *             enabled?: bool|Param, // Default: false
 *             formats?: list<scalar|null|Param>,
 *             extensions?: list<scalar|null|Param>,
 *         },
 *     },
 *     translator?: bool|array{ // Translator configuration
 *         enabled?: bool|Param, // Default: true
 *         fallbacks?: list<scalar|null|Param>,
 *         logging?: bool|Param, // Default: false
 *         formatter?: scalar|null|Param, // Default: "translator.formatter.default"
 *         cache_dir?: scalar|null|Param, // Default: "%kernel.cache_dir%/translations"
 *         default_path?: scalar|null|Param, // The default path used to load translations. // Default: "%kernel.project_dir%/translations"
 *         paths?: list<scalar|null|Param>,
 *         pseudo_localization?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             accents?: bool|Param, // Default: true
 *             expansion_factor?: float|Param, // Default: 1.0
 *             brackets?: bool|Param, // Default: true
 *             parse_html?: bool|Param, // Default: false
 *             localizable_html_attributes?: list<scalar|null|Param>,
 *         },
 *         providers?: array<string, array{ // Default: []
 *             dsn?: scalar|null|Param,
 *             domains?: list<scalar|null|Param>,
 *             locales?: list<scalar|null|Param>,
 *         }>,
 *         globals?: array<string, string|array{ // Default: []
 *             value?: mixed,
 *             message?: string|Param,
 *             parameters?: array<string, scalar|null|Param>,
 *             domain?: string|Param,
 *         }>,
 *     },
 *     validation?: bool|array{ // Validation configuration
 *         enabled?: bool|Param, // Default: true
 *         cache?: scalar|null|Param, // Deprecated: Setting the "framework.validation.cache.cache" configuration option is deprecated. It will be removed in version 8.0.
 *         enable_attributes?: bool|Param, // Default: true
 *         static_method?: list<scalar|null|Param>,
 *         translation_domain?: scalar|null|Param, // Default: "validators"
 *         email_validation_mode?: "html5"|"html5-allow-no-tld"|"strict"|"loose"|Param, // Default: "html5"
 *         mapping?: array{
 *             paths?: list<scalar|null|Param>,
 *         },
 *         not_compromised_password?: bool|array{
 *             enabled?: bool|Param, // When disabled, compromised passwords will be accepted as valid. // Default: true
 *             endpoint?: scalar|null|Param, // API endpoint for the NotCompromisedPassword Validator. // Default: null
 *         },
 *         disable_translation?: bool|Param, // Default: false
 *         auto_mapping?: array<string, array{ // Default: []
 *             services?: list<scalar|null|Param>,
 *         }>,
 *     },
 *     annotations?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     serializer?: bool|array{ // Serializer configuration
 *         enabled?: bool|Param, // Default: true
 *         enable_attributes?: bool|Param, // Default: true
 *         name_converter?: scalar|null|Param,
 *         circular_reference_handler?: scalar|null|Param,
 *         max_depth_handler?: scalar|null|Param,
 *         mapping?: array{
 *             paths?: list<scalar|null|Param>,
 *         },
 *         default_context?: list<mixed>,
 *         named_serializers?: array<string, array{ // Default: []
 *             name_converter?: scalar|null|Param,
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
 *         aliases?: array<string, scalar|null|Param>,
 *     },
 *     property_info?: bool|array{ // Property info configuration
 *         enabled?: bool|Param, // Default: true
 *         with_constructor_extractor?: bool|Param, // Registers the constructor extractor.
 *     },
 *     cache?: array{ // Cache configuration
 *         prefix_seed?: scalar|null|Param, // Used to namespace cache keys when using several apps with the same shared backend. // Default: "_%kernel.project_dir%.%kernel.container_class%"
 *         app?: scalar|null|Param, // App related cache pools configuration. // Default: "cache.adapter.filesystem"
 *         system?: scalar|null|Param, // System related cache pools configuration. // Default: "cache.adapter.system"
 *         directory?: scalar|null|Param, // Default: "%kernel.share_dir%/pools/app"
 *         default_psr6_provider?: scalar|null|Param,
 *         default_redis_provider?: scalar|null|Param, // Default: "redis://localhost"
 *         default_valkey_provider?: scalar|null|Param, // Default: "valkey://localhost"
 *         default_memcached_provider?: scalar|null|Param, // Default: "memcached://localhost"
 *         default_doctrine_dbal_provider?: scalar|null|Param, // Default: "database_connection"
 *         default_pdo_provider?: scalar|null|Param, // Default: null
 *         pools?: array<string, array{ // Default: []
 *             adapters?: list<scalar|null|Param>,
 *             tags?: scalar|null|Param, // Default: null
 *             public?: bool|Param, // Default: false
 *             default_lifetime?: scalar|null|Param, // Default lifetime of the pool.
 *             provider?: scalar|null|Param, // Overwrite the setting from the default provider for this adapter.
 *             early_expiration_message_bus?: scalar|null|Param,
 *             clearer?: scalar|null|Param,
 *         }>,
 *     },
 *     php_errors?: array{ // PHP errors handling configuration
 *         log?: mixed, // Use the application logger instead of the PHP logger for logging PHP errors. // Default: true
 *         throw?: bool|Param, // Throw PHP errors as \ErrorException instances. // Default: true
 *     },
 *     exceptions?: array<string, array{ // Default: []
 *         log_level?: scalar|null|Param, // The level of log message. Null to let Symfony decide. // Default: null
 *         status_code?: scalar|null|Param, // The status code of the response. Null or 0 to let Symfony decide. // Default: null
 *         log_channel?: scalar|null|Param, // The channel of log message. Null to let Symfony decide. // Default: null
 *     }>,
 *     web_link?: bool|array{ // Web links configuration
 *         enabled?: bool|Param, // Default: true
 *     },
 *     lock?: bool|string|array{ // Lock configuration
 *         enabled?: bool|Param, // Default: true
 *         resources?: array<string, string|list<scalar|null|Param>>,
 *     },
 *     semaphore?: bool|string|array{ // Semaphore configuration
 *         enabled?: bool|Param, // Default: false
 *         resources?: array<string, scalar|null|Param>,
 *     },
 *     messenger?: bool|array{ // Messenger configuration
 *         enabled?: bool|Param, // Default: true
 *         routing?: array<string, array{ // Default: []
 *             senders?: list<scalar|null|Param>,
 *         }>,
 *         serializer?: array{
 *             default_serializer?: scalar|null|Param, // Service id to use as the default serializer for the transports. // Default: "messenger.transport.native_php_serializer"
 *             symfony_serializer?: array{
 *                 format?: scalar|null|Param, // Serialization format for the messenger.transport.symfony_serializer service (which is not the serializer used by default). // Default: "json"
 *                 context?: array<string, mixed>,
 *             },
 *         },
 *         transports?: array<string, string|array{ // Default: []
 *             dsn?: scalar|null|Param,
 *             serializer?: scalar|null|Param, // Service id of a custom serializer to use. // Default: null
 *             options?: list<mixed>,
 *             failure_transport?: scalar|null|Param, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *             retry_strategy?: string|array{
 *                 service?: scalar|null|Param, // Service id to override the retry strategy entirely. // Default: null
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: this delay = (delay * (multiple ^ retries)). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness to apply to the delay (between 0 and 1). // Default: 0.1
 *             },
 *             rate_limiter?: scalar|null|Param, // Rate limiter name to use when processing messages. // Default: null
 *         }>,
 *         failure_transport?: scalar|null|Param, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *         stop_worker_on_signals?: list<scalar|null|Param>,
 *         default_bus?: scalar|null|Param, // Default: null
 *         buses?: array<string, array{ // Default: {"messenger.bus.default":{"default_middleware":{"enabled":true,"allow_no_handlers":false,"allow_no_senders":true},"middleware":[]}}
 *             default_middleware?: bool|string|array{
 *                 enabled?: bool|Param, // Default: true
 *                 allow_no_handlers?: bool|Param, // Default: false
 *                 allow_no_senders?: bool|Param, // Default: true
 *             },
 *             middleware?: list<string|array{ // Default: []
 *                 id: scalar|null|Param,
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
 *             http_version?: scalar|null|Param, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|null|Param>,
 *             proxy?: scalar|null|Param, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|null|Param, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|null|Param, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|null|Param, // A certificate authority file.
 *             capath?: scalar|null|Param, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|null|Param, // A PEM formatted certificate file.
 *             local_pk?: scalar|null|Param, // A private key file.
 *             passphrase?: scalar|null|Param, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|null|Param, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...)
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|null|Param, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|null|Param, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|null|Param, // service id to override the retry strategy. // Default: null
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
 *         mock_response_factory?: scalar|null|Param, // The id of the service that should generate mock responses. It should be either an invokable or an iterable.
 *         scoped_clients?: array<string, string|array{ // Default: []
 *             scope?: scalar|null|Param, // The regular expression that the request URL must match before adding the other options. When none is provided, the base URI is used instead.
 *             base_uri?: scalar|null|Param, // The URI to resolve relative URLs, following rules in RFC 3985, section 2.
 *             auth_basic?: scalar|null|Param, // An HTTP Basic authentication "username:password".
 *             auth_bearer?: scalar|null|Param, // A token enabling HTTP Bearer authorization.
 *             auth_ntlm?: scalar|null|Param, // A "username:password" pair to use Microsoft NTLM authentication (requires the cURL extension).
 *             query?: array<string, scalar|null|Param>,
 *             headers?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|null|Param, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|null|Param>,
 *             proxy?: scalar|null|Param, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|null|Param, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|null|Param, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|null|Param, // A certificate authority file.
 *             capath?: scalar|null|Param, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|null|Param, // A PEM formatted certificate file.
 *             local_pk?: scalar|null|Param, // A private key file.
 *             passphrase?: scalar|null|Param, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|null|Param, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...).
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|null|Param, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|null|Param, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. Null means no cap. // Default: null
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|null|Param, // service id to override the retry strategy. // Default: null
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
 *         message_bus?: scalar|null|Param, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         dsn?: scalar|null|Param, // Default: null
 *         transports?: array<string, scalar|null|Param>,
 *         envelope?: array{ // Mailer Envelope configuration
 *             sender?: scalar|null|Param,
 *             recipients?: list<scalar|null|Param>,
 *             allowed_recipients?: list<scalar|null|Param>,
 *         },
 *         headers?: array<string, string|array{ // Default: []
 *             value?: mixed,
 *         }>,
 *         dkim_signer?: bool|array{ // DKIM signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|null|Param, // Key content, or path to key (in PEM format with the `file://` prefix) // Default: ""
 *             domain?: scalar|null|Param, // Default: ""
 *             select?: scalar|null|Param, // Default: ""
 *             passphrase?: scalar|null|Param, // The private key passphrase // Default: ""
 *             options?: array<string, mixed>,
 *         },
 *         smime_signer?: bool|array{ // S/MIME signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|null|Param, // Path to key (in PEM format) // Default: ""
 *             certificate?: scalar|null|Param, // Path to certificate (in PEM format without the `file://` prefix) // Default: ""
 *             passphrase?: scalar|null|Param, // The private key passphrase // Default: null
 *             extra_certificates?: scalar|null|Param, // Default: null
 *             sign_options?: int|Param, // Default: null
 *         },
 *         smime_encrypter?: bool|array{ // S/MIME encrypter configuration
 *             enabled?: bool|Param, // Default: false
 *             repository?: scalar|null|Param, // S/MIME certificate repository service. This service shall implement the `Symfony\Component\Mailer\EventListener\SmimeCertificateRepositoryInterface`. // Default: ""
 *             cipher?: int|Param, // A set of algorithms used to encrypt the message // Default: null
 *         },
 *     },
 *     secrets?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         vault_directory?: scalar|null|Param, // Default: "%kernel.project_dir%/config/secrets/%kernel.runtime_environment%"
 *         local_dotenv_file?: scalar|null|Param, // Default: "%kernel.project_dir%/.env.%kernel.runtime_environment%.local"
 *         decryption_env_var?: scalar|null|Param, // Default: "base64:default::SYMFONY_DECRYPTION_SECRET"
 *     },
 *     notifier?: bool|array{ // Notifier configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|null|Param, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         chatter_transports?: array<string, scalar|null|Param>,
 *         texter_transports?: array<string, scalar|null|Param>,
 *         notification_on_failed_messages?: bool|Param, // Default: false
 *         channel_policy?: array<string, string|list<scalar|null|Param>>,
 *         admin_recipients?: list<array{ // Default: []
 *             email?: scalar|null|Param,
 *             phone?: scalar|null|Param, // Default: ""
 *         }>,
 *     },
 *     rate_limiter?: bool|array{ // Rate limiter configuration
 *         enabled?: bool|Param, // Default: true
 *         limiters?: array<string, array{ // Default: []
 *             lock_factory?: scalar|null|Param, // The service ID of the lock factory used by this limiter (or null to disable locking). // Default: "auto"
 *             cache_pool?: scalar|null|Param, // The cache pool to use for storing the current limiter state. // Default: "cache.rate_limiter"
 *             storage_service?: scalar|null|Param, // The service ID of a custom storage implementation, this precedes any configured "cache_pool". // Default: null
 *             policy: "fixed_window"|"token_bucket"|"sliding_window"|"compound"|"no_limit"|Param, // The algorithm to be used by this limiter.
 *             limiters?: list<scalar|null|Param>,
 *             limit?: int|Param, // The maximum allowed hits in a fixed interval or burst.
 *             interval?: scalar|null|Param, // Configures the fixed interval if "policy" is set to "fixed_window" or "sliding_window". The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *             rate?: array{ // Configures the fill rate if "policy" is set to "token_bucket".
 *                 interval?: scalar|null|Param, // Configures the rate interval. The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *                 amount?: int|Param, // Amount of tokens to add each interval. // Default: 1
 *             },
 *         }>,
 *     },
 *     uid?: bool|array{ // Uid configuration
 *         enabled?: bool|Param, // Default: true
 *         default_uuid_version?: 7|6|4|1|Param, // Default: 7
 *         name_based_uuid_version?: 5|3|Param, // Default: 5
 *         name_based_uuid_namespace?: scalar|null|Param,
 *         time_based_uuid_version?: 7|6|1|Param, // Default: 7
 *         time_based_uuid_node?: scalar|null|Param,
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
 *         message_bus?: scalar|null|Param, // The message bus to use. // Default: "messenger.default_bus"
 *         routing?: array<string, array{ // Default: []
 *             service: scalar|null|Param,
 *             secret?: scalar|null|Param, // Default: ""
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
 *     access_denied_url?: scalar|null|Param, // Default: null
 *     session_fixation_strategy?: "none"|"migrate"|"invalidate"|Param, // Default: "migrate"
 *     hide_user_not_found?: bool|Param, // Deprecated: The "hide_user_not_found" option is deprecated and will be removed in 8.0. Use the "expose_security_errors" option instead.
 *     expose_security_errors?: \Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::None|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::AccountStatus|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::All|Param, // Default: "none"
 *     erase_credentials?: bool|Param, // Default: true
 *     access_decision_manager?: array{
 *         strategy?: "affirmative"|"consensus"|"unanimous"|"priority"|Param,
 *         service?: scalar|null|Param,
 *         strategy_service?: scalar|null|Param,
 *         allow_if_all_abstain?: bool|Param, // Default: false
 *         allow_if_equal_granted_denied?: bool|Param, // Default: true
 *     },
 *     password_hashers?: array<string, string|array{ // Default: []
 *         algorithm?: scalar|null|Param,
 *         migrate_from?: list<scalar|null|Param>,
 *         hash_algorithm?: scalar|null|Param, // Name of hashing algorithm for PBKDF2 (i.e. sha256, sha512, etc..) See hash_algos() for a list of supported algorithms. // Default: "sha512"
 *         key_length?: scalar|null|Param, // Default: 40
 *         ignore_case?: bool|Param, // Default: false
 *         encode_as_base64?: bool|Param, // Default: true
 *         iterations?: scalar|null|Param, // Default: 5000
 *         cost?: int|Param, // Default: null
 *         memory_cost?: scalar|null|Param, // Default: null
 *         time_cost?: scalar|null|Param, // Default: null
 *         id?: scalar|null|Param,
 *     }>,
 *     providers?: array<string, array{ // Default: []
 *         id?: scalar|null|Param,
 *         chain?: array{
 *             providers?: list<scalar|null|Param>,
 *         },
 *         memory?: array{
 *             users?: array<string, array{ // Default: []
 *                 password?: scalar|null|Param, // Default: null
 *                 roles?: list<scalar|null|Param>,
 *             }>,
 *         },
 *         ldap?: array{
 *             service: scalar|null|Param,
 *             base_dn: scalar|null|Param,
 *             search_dn?: scalar|null|Param, // Default: null
 *             search_password?: scalar|null|Param, // Default: null
 *             extra_fields?: list<scalar|null|Param>,
 *             default_roles?: list<scalar|null|Param>,
 *             role_fetcher?: scalar|null|Param, // Default: null
 *             uid_key?: scalar|null|Param, // Default: "sAMAccountName"
 *             filter?: scalar|null|Param, // Default: "({uid_key}={user_identifier})"
 *             password_attribute?: scalar|null|Param, // Default: null
 *         },
 *         entity?: array{
 *             class: scalar|null|Param, // The full entity class name of your user class.
 *             property?: scalar|null|Param, // Default: null
 *             manager_name?: scalar|null|Param, // Default: null
 *         },
 *     }>,
 *     firewalls: array<string, array{ // Default: []
 *         pattern?: scalar|null|Param,
 *         host?: scalar|null|Param,
 *         methods?: list<scalar|null|Param>,
 *         security?: bool|Param, // Default: true
 *         user_checker?: scalar|null|Param, // The UserChecker to use when authenticating users in this firewall. // Default: "security.user_checker"
 *         request_matcher?: scalar|null|Param,
 *         access_denied_url?: scalar|null|Param,
 *         access_denied_handler?: scalar|null|Param,
 *         entry_point?: scalar|null|Param, // An enabled authenticator name or a service id that implements "Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface".
 *         provider?: scalar|null|Param,
 *         stateless?: bool|Param, // Default: false
 *         lazy?: bool|Param, // Default: false
 *         context?: scalar|null|Param,
 *         logout?: array{
 *             enable_csrf?: bool|null|Param, // Default: null
 *             csrf_token_id?: scalar|null|Param, // Default: "logout"
 *             csrf_parameter?: scalar|null|Param, // Default: "_csrf_token"
 *             csrf_token_manager?: scalar|null|Param,
 *             path?: scalar|null|Param, // Default: "/logout"
 *             target?: scalar|null|Param, // Default: "/"
 *             invalidate_session?: bool|Param, // Default: true
 *             clear_site_data?: list<"*"|"cache"|"cookies"|"storage"|"executionContexts"|Param>,
 *             delete_cookies?: array<string, array{ // Default: []
 *                 path?: scalar|null|Param, // Default: null
 *                 domain?: scalar|null|Param, // Default: null
 *                 secure?: scalar|null|Param, // Default: false
 *                 samesite?: scalar|null|Param, // Default: null
 *                 partitioned?: scalar|null|Param, // Default: false
 *             }>,
 *         },
 *         switch_user?: array{
 *             provider?: scalar|null|Param,
 *             parameter?: scalar|null|Param, // Default: "_switch_user"
 *             role?: scalar|null|Param, // Default: "ROLE_ALLOWED_TO_SWITCH"
 *             target_route?: scalar|null|Param, // Default: null
 *         },
 *         required_badges?: list<scalar|null|Param>,
 *         custom_authenticators?: list<scalar|null|Param>,
 *         login_throttling?: array{
 *             limiter?: scalar|null|Param, // A service id implementing "Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface".
 *             max_attempts?: int|Param, // Default: 5
 *             interval?: scalar|null|Param, // Default: "1 minute"
 *             lock_factory?: scalar|null|Param, // The service ID of the lock factory used by the login rate limiter (or null to disable locking). // Default: null
 *             cache_pool?: string|Param, // The cache pool to use for storing the limiter state // Default: "cache.rate_limiter"
 *             storage_service?: string|Param, // The service ID of a custom storage implementation, this precedes any configured "cache_pool" // Default: null
 *         },
 *         two_factor?: array{
 *             check_path?: scalar|null|Param, // Default: "/2fa_check"
 *             post_only?: bool|Param, // Default: true
 *             auth_form_path?: scalar|null|Param, // Default: "/2fa"
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|null|Param, // Default: "/"
 *             success_handler?: scalar|null|Param, // Default: null
 *             failure_handler?: scalar|null|Param, // Default: null
 *             authentication_required_handler?: scalar|null|Param, // Default: null
 *             auth_code_parameter_name?: scalar|null|Param, // Default: "_auth_code"
 *             trusted_parameter_name?: scalar|null|Param, // Default: "_trusted"
 *             remember_me_sets_trusted?: scalar|null|Param, // Default: false
 *             multi_factor?: bool|Param, // Default: false
 *             prepare_on_login?: bool|Param, // Default: false
 *             prepare_on_access_denied?: bool|Param, // Default: false
 *             enable_csrf?: scalar|null|Param, // Default: false
 *             csrf_parameter?: scalar|null|Param, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|null|Param, // Default: "two_factor"
 *             csrf_header?: scalar|null|Param, // Default: null
 *             csrf_token_manager?: scalar|null|Param, // Default: "scheb_two_factor.csrf_token_manager"
 *             provider?: scalar|null|Param, // Default: null
 *         },
 *         x509?: array{
 *             provider?: scalar|null|Param,
 *             user?: scalar|null|Param, // Default: "SSL_CLIENT_S_DN_Email"
 *             credentials?: scalar|null|Param, // Default: "SSL_CLIENT_S_DN"
 *             user_identifier?: scalar|null|Param, // Default: "emailAddress"
 *         },
 *         remote_user?: array{
 *             provider?: scalar|null|Param,
 *             user?: scalar|null|Param, // Default: "REMOTE_USER"
 *         },
 *         login_link?: array{
 *             check_route: scalar|null|Param, // Route that will validate the login link - e.g. "app_login_link_verify".
 *             check_post_only?: scalar|null|Param, // If true, only HTTP POST requests to "check_route" will be handled by the authenticator. // Default: false
 *             signature_properties: list<scalar|null|Param>,
 *             lifetime?: int|Param, // The lifetime of the login link in seconds. // Default: 600
 *             max_uses?: int|Param, // Max number of times a login link can be used - null means unlimited within lifetime. // Default: null
 *             used_link_cache?: scalar|null|Param, // Cache service id used to expired links of max_uses is set.
 *             success_handler?: scalar|null|Param, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface.
 *             failure_handler?: scalar|null|Param, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface.
 *             provider?: scalar|null|Param, // The user provider to load users from.
 *             secret?: scalar|null|Param, // Default: "%kernel.secret%"
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|null|Param, // Default: "/"
 *             login_path?: scalar|null|Param, // Default: "/login"
 *             target_path_parameter?: scalar|null|Param, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|null|Param, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|null|Param, // Default: "_failure_path"
 *         },
 *         form_login?: array{
 *             provider?: scalar|null|Param,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|null|Param,
 *             failure_handler?: scalar|null|Param,
 *             check_path?: scalar|null|Param, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|null|Param, // Default: "/login"
 *             username_parameter?: scalar|null|Param, // Default: "_username"
 *             password_parameter?: scalar|null|Param, // Default: "_password"
 *             csrf_parameter?: scalar|null|Param, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|null|Param, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|null|Param, // Default: "/"
 *             target_path_parameter?: scalar|null|Param, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|null|Param, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|null|Param, // Default: "_failure_path"
 *         },
 *         form_login_ldap?: array{
 *             provider?: scalar|null|Param,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|null|Param,
 *             failure_handler?: scalar|null|Param,
 *             check_path?: scalar|null|Param, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|null|Param, // Default: "/login"
 *             username_parameter?: scalar|null|Param, // Default: "_username"
 *             password_parameter?: scalar|null|Param, // Default: "_password"
 *             csrf_parameter?: scalar|null|Param, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|null|Param, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|null|Param, // Default: "/"
 *             target_path_parameter?: scalar|null|Param, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|null|Param, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|null|Param, // Default: "_failure_path"
 *             service?: scalar|null|Param, // Default: "ldap"
 *             dn_string?: scalar|null|Param, // Default: "{user_identifier}"
 *             query_string?: scalar|null|Param,
 *             search_dn?: scalar|null|Param, // Default: ""
 *             search_password?: scalar|null|Param, // Default: ""
 *         },
 *         json_login?: array{
 *             provider?: scalar|null|Param,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|null|Param,
 *             failure_handler?: scalar|null|Param,
 *             check_path?: scalar|null|Param, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|null|Param, // Default: "/login"
 *             username_path?: scalar|null|Param, // Default: "username"
 *             password_path?: scalar|null|Param, // Default: "password"
 *         },
 *         json_login_ldap?: array{
 *             provider?: scalar|null|Param,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|null|Param,
 *             failure_handler?: scalar|null|Param,
 *             check_path?: scalar|null|Param, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|null|Param, // Default: "/login"
 *             username_path?: scalar|null|Param, // Default: "username"
 *             password_path?: scalar|null|Param, // Default: "password"
 *             service?: scalar|null|Param, // Default: "ldap"
 *             dn_string?: scalar|null|Param, // Default: "{user_identifier}"
 *             query_string?: scalar|null|Param,
 *             search_dn?: scalar|null|Param, // Default: ""
 *             search_password?: scalar|null|Param, // Default: ""
 *         },
 *         access_token?: array{
 *             provider?: scalar|null|Param,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|null|Param,
 *             failure_handler?: scalar|null|Param,
 *             realm?: scalar|null|Param, // Default: null
 *             token_extractors?: list<scalar|null|Param>,
 *             token_handler: string|array{
 *                 id?: scalar|null|Param,
 *                 oidc_user_info?: string|array{
 *                     base_uri: scalar|null|Param, // Base URI of the userinfo endpoint on the OIDC server, or the OIDC server URI to use the discovery (require "discovery" to be configured).
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         cache?: array{
 *                             id: scalar|null|Param, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                     },
 *                     claim?: scalar|null|Param, // Claim which contains the user identifier (e.g. sub, email, etc.). // Default: "sub"
 *                     client?: scalar|null|Param, // HttpClient service id to use to call the OIDC server.
 *                 },
 *                 oidc?: array{
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         base_uri: list<scalar|null|Param>,
 *                         cache?: array{
 *                             id: scalar|null|Param, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                     },
 *                     claim?: scalar|null|Param, // Claim which contains the user identifier (e.g.: sub, email..). // Default: "sub"
 *                     audience: scalar|null|Param, // Audience set in the token, for validation purpose.
 *                     issuers: list<scalar|null|Param>,
 *                     algorithm?: array<mixed>,
 *                     algorithms: list<scalar|null|Param>,
 *                     key?: scalar|null|Param, // Deprecated: The "key" option is deprecated and will be removed in 8.0. Use the "keyset" option instead. // JSON-encoded JWK used to sign the token (must contain a "kty" key).
 *                     keyset?: scalar|null|Param, // JSON-encoded JWKSet used to sign the token (must contain a list of valid public keys).
 *                     encryption?: bool|array{
 *                         enabled?: bool|Param, // Default: false
 *                         enforce?: bool|Param, // When enabled, the token shall be encrypted. // Default: false
 *                         algorithms: list<scalar|null|Param>,
 *                         keyset: scalar|null|Param, // JSON-encoded JWKSet used to decrypt the token (must contain a list of valid private keys).
 *                     },
 *                 },
 *                 cas?: array{
 *                     validation_url: scalar|null|Param, // CAS server validation URL
 *                     prefix?: scalar|null|Param, // CAS prefix // Default: "cas"
 *                     http_client?: scalar|null|Param, // HTTP Client service // Default: null
 *                 },
 *                 oauth2?: scalar|null|Param,
 *             },
 *         },
 *         http_basic?: array{
 *             provider?: scalar|null|Param,
 *             realm?: scalar|null|Param, // Default: "Secured Area"
 *         },
 *         http_basic_ldap?: array{
 *             provider?: scalar|null|Param,
 *             realm?: scalar|null|Param, // Default: "Secured Area"
 *             service?: scalar|null|Param, // Default: "ldap"
 *             dn_string?: scalar|null|Param, // Default: "{user_identifier}"
 *             query_string?: scalar|null|Param,
 *             search_dn?: scalar|null|Param, // Default: ""
 *             search_password?: scalar|null|Param, // Default: ""
 *         },
 *         remember_me?: array{
 *             secret?: scalar|null|Param, // Default: "%kernel.secret%"
 *             service?: scalar|null|Param,
 *             user_providers?: list<scalar|null|Param>,
 *             catch_exceptions?: bool|Param, // Default: true
 *             signature_properties?: list<scalar|null|Param>,
 *             token_provider?: string|array{
 *                 service?: scalar|null|Param, // The service ID of a custom remember-me token provider.
 *                 doctrine?: bool|array{
 *                     enabled?: bool|Param, // Default: false
 *                     connection?: scalar|null|Param, // Default: null
 *                 },
 *             },
 *             token_verifier?: scalar|null|Param, // The service ID of a custom rememberme token verifier.
 *             name?: scalar|null|Param, // Default: "REMEMBERME"
 *             lifetime?: int|Param, // Default: 31536000
 *             path?: scalar|null|Param, // Default: "/"
 *             domain?: scalar|null|Param, // Default: null
 *             secure?: true|false|"auto"|Param, // Default: null
 *             httponly?: bool|Param, // Default: true
 *             samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *             always_remember_me?: bool|Param, // Default: false
 *             remember_me_parameter?: scalar|null|Param, // Default: "_remember_me"
 *         },
 *     }>,
 *     access_control?: list<array{ // Default: []
 *         request_matcher?: scalar|null|Param, // Default: null
 *         requires_channel?: scalar|null|Param, // Default: null
 *         path?: scalar|null|Param, // Use the urldecoded format. // Default: null
 *         host?: scalar|null|Param, // Default: null
 *         port?: int|Param, // Default: null
 *         ips?: list<scalar|null|Param>,
 *         attributes?: array<string, scalar|null|Param>,
 *         route?: scalar|null|Param, // Default: null
 *         methods?: list<scalar|null|Param>,
 *         allow_if?: scalar|null|Param, // Default: null
 *         roles?: list<scalar|null|Param>,
 *     }>,
 *     role_hierarchy?: array<string, string|list<scalar|null|Param>>,
 * }
 * @psalm-type TwigConfig = array{
 *     form_themes?: list<scalar|null|Param>,
 *     globals?: array<string, array{ // Default: []
 *         id?: scalar|null|Param,
 *         type?: scalar|null|Param,
 *         value?: mixed,
 *     }>,
 *     autoescape_service?: scalar|null|Param, // Default: null
 *     autoescape_service_method?: scalar|null|Param, // Default: null
 *     base_template_class?: scalar|null|Param, // Deprecated: The child node "base_template_class" at path "twig.base_template_class" is deprecated.
 *     cache?: scalar|null|Param, // Default: true
 *     charset?: scalar|null|Param, // Default: "%kernel.charset%"
 *     debug?: bool|Param, // Default: "%kernel.debug%"
 *     strict_variables?: bool|Param, // Default: "%kernel.debug%"
 *     auto_reload?: scalar|null|Param,
 *     optimizations?: int|Param,
 *     default_path?: scalar|null|Param, // The default path used to load templates. // Default: "%kernel.project_dir%/templates"
 *     file_name_pattern?: list<scalar|null|Param>,
 *     paths?: array<string, mixed>,
 *     date?: array{ // The default format options used by the date filter.
 *         format?: scalar|null|Param, // Default: "F j, Y H:i"
 *         interval_format?: scalar|null|Param, // Default: "%d days"
 *         timezone?: scalar|null|Param, // The timezone used when formatting dates, when set to null, the timezone returned by date_default_timezone_get() is used. // Default: null
 *     },
 *     number_format?: array{ // The default format options for the number_format filter.
 *         decimals?: int|Param, // Default: 0
 *         decimal_point?: scalar|null|Param, // Default: "."
 *         thousands_separator?: scalar|null|Param, // Default: ","
 *     },
 *     mailer?: array{
 *         html_to_text_converter?: scalar|null|Param, // A service implementing the "Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface". // Default: null
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
 *             block_separator?: scalar|null|Param,
 *             inner_separator?: scalar|null|Param,
 *             soft_break?: scalar|null|Param,
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
 *             unordered_list_markers?: list<scalar|null|Param>,
 *         },
 *         ...<mixed>
 *     },
 * }
 * @psalm-type MonologConfig = array{
 *     use_microseconds?: scalar|null|Param, // Default: true
 *     channels?: list<scalar|null|Param>,
 *     handlers?: array<string, array{ // Default: []
 *         type: scalar|null|Param,
 *         id?: scalar|null|Param,
 *         enabled?: bool|Param, // Default: true
 *         priority?: scalar|null|Param, // Default: 0
 *         level?: scalar|null|Param, // Default: "DEBUG"
 *         bubble?: bool|Param, // Default: true
 *         interactive_only?: bool|Param, // Default: false
 *         app_name?: scalar|null|Param, // Default: null
 *         fill_extra_context?: bool|Param, // Default: false
 *         include_stacktraces?: bool|Param, // Default: false
 *         process_psr_3_messages?: array{
 *             enabled?: bool|null|Param, // Default: null
 *             date_format?: scalar|null|Param,
 *             remove_used_context_fields?: bool|Param,
 *         },
 *         path?: scalar|null|Param, // Default: "%kernel.logs_dir%/%kernel.environment%.log"
 *         file_permission?: scalar|null|Param, // Default: null
 *         use_locking?: bool|Param, // Default: false
 *         filename_format?: scalar|null|Param, // Default: "{filename}-{date}"
 *         date_format?: scalar|null|Param, // Default: "Y-m-d"
 *         ident?: scalar|null|Param, // Default: false
 *         logopts?: scalar|null|Param, // Default: 1
 *         facility?: scalar|null|Param, // Default: "user"
 *         max_files?: scalar|null|Param, // Default: 0
 *         action_level?: scalar|null|Param, // Default: "WARNING"
 *         activation_strategy?: scalar|null|Param, // Default: null
 *         stop_buffering?: bool|Param, // Default: true
 *         passthru_level?: scalar|null|Param, // Default: null
 *         excluded_404s?: list<scalar|null|Param>,
 *         excluded_http_codes?: list<array{ // Default: []
 *             code?: scalar|null|Param,
 *             urls?: list<scalar|null|Param>,
 *         }>,
 *         accepted_levels?: list<scalar|null|Param>,
 *         min_level?: scalar|null|Param, // Default: "DEBUG"
 *         max_level?: scalar|null|Param, // Default: "EMERGENCY"
 *         buffer_size?: scalar|null|Param, // Default: 0
 *         flush_on_overflow?: bool|Param, // Default: false
 *         handler?: scalar|null|Param,
 *         url?: scalar|null|Param,
 *         exchange?: scalar|null|Param,
 *         exchange_name?: scalar|null|Param, // Default: "log"
 *         room?: scalar|null|Param,
 *         message_format?: scalar|null|Param, // Default: "text"
 *         api_version?: scalar|null|Param, // Default: null
 *         channel?: scalar|null|Param, // Default: null
 *         bot_name?: scalar|null|Param, // Default: "Monolog"
 *         use_attachment?: scalar|null|Param, // Default: true
 *         use_short_attachment?: scalar|null|Param, // Default: false
 *         include_extra?: scalar|null|Param, // Default: false
 *         icon_emoji?: scalar|null|Param, // Default: null
 *         webhook_url?: scalar|null|Param,
 *         exclude_fields?: list<scalar|null|Param>,
 *         team?: scalar|null|Param,
 *         notify?: scalar|null|Param, // Default: false
 *         nickname?: scalar|null|Param, // Default: "Monolog"
 *         token?: scalar|null|Param,
 *         region?: scalar|null|Param,
 *         source?: scalar|null|Param,
 *         use_ssl?: bool|Param, // Default: true
 *         user?: mixed,
 *         title?: scalar|null|Param, // Default: null
 *         host?: scalar|null|Param, // Default: null
 *         port?: scalar|null|Param, // Default: 514
 *         config?: list<scalar|null|Param>,
 *         members?: list<scalar|null|Param>,
 *         connection_string?: scalar|null|Param,
 *         timeout?: scalar|null|Param,
 *         time?: scalar|null|Param, // Default: 60
 *         deduplication_level?: scalar|null|Param, // Default: 400
 *         store?: scalar|null|Param, // Default: null
 *         connection_timeout?: scalar|null|Param,
 *         persistent?: bool|Param,
 *         dsn?: scalar|null|Param,
 *         hub_id?: scalar|null|Param, // Default: null
 *         client_id?: scalar|null|Param, // Default: null
 *         auto_log_stacks?: scalar|null|Param, // Default: false
 *         release?: scalar|null|Param, // Default: null
 *         environment?: scalar|null|Param, // Default: null
 *         message_type?: scalar|null|Param, // Default: 0
 *         parse_mode?: scalar|null|Param, // Default: null
 *         disable_webpage_preview?: bool|null|Param, // Default: null
 *         disable_notification?: bool|null|Param, // Default: null
 *         split_long_messages?: bool|Param, // Default: false
 *         delay_between_messages?: bool|Param, // Default: false
 *         topic?: int|Param, // Default: null
 *         factor?: int|Param, // Default: 1
 *         tags?: list<scalar|null|Param>,
 *         console_formater_options?: mixed, // Deprecated: "monolog.handlers..console_formater_options.console_formater_options" is deprecated, use "monolog.handlers..console_formater_options.console_formatter_options" instead.
 *         console_formatter_options?: mixed, // Default: []
 *         formatter?: scalar|null|Param,
 *         nested?: bool|Param, // Default: false
 *         publisher?: string|array{
 *             id?: scalar|null|Param,
 *             hostname?: scalar|null|Param,
 *             port?: scalar|null|Param, // Default: 12201
 *             chunk_size?: scalar|null|Param, // Default: 1420
 *             encoder?: "json"|"compressed_json"|Param,
 *         },
 *         mongo?: string|array{
 *             id?: scalar|null|Param,
 *             host?: scalar|null|Param,
 *             port?: scalar|null|Param, // Default: 27017
 *             user?: scalar|null|Param,
 *             pass?: scalar|null|Param,
 *             database?: scalar|null|Param, // Default: "monolog"
 *             collection?: scalar|null|Param, // Default: "logs"
 *         },
 *         mongodb?: string|array{
 *             id?: scalar|null|Param, // ID of a MongoDB\Client service
 *             uri?: scalar|null|Param,
 *             username?: scalar|null|Param,
 *             password?: scalar|null|Param,
 *             database?: scalar|null|Param, // Default: "monolog"
 *             collection?: scalar|null|Param, // Default: "logs"
 *         },
 *         elasticsearch?: string|array{
 *             id?: scalar|null|Param,
 *             hosts?: list<scalar|null|Param>,
 *             host?: scalar|null|Param,
 *             port?: scalar|null|Param, // Default: 9200
 *             transport?: scalar|null|Param, // Default: "Http"
 *             user?: scalar|null|Param, // Default: null
 *             password?: scalar|null|Param, // Default: null
 *         },
 *         index?: scalar|null|Param, // Default: "monolog"
 *         document_type?: scalar|null|Param, // Default: "logs"
 *         ignore_error?: scalar|null|Param, // Default: false
 *         redis?: string|array{
 *             id?: scalar|null|Param,
 *             host?: scalar|null|Param,
 *             password?: scalar|null|Param, // Default: null
 *             port?: scalar|null|Param, // Default: 6379
 *             database?: scalar|null|Param, // Default: 0
 *             key_name?: scalar|null|Param, // Default: "monolog_redis"
 *         },
 *         predis?: string|array{
 *             id?: scalar|null|Param,
 *             host?: scalar|null|Param,
 *         },
 *         from_email?: scalar|null|Param,
 *         to_email?: list<scalar|null|Param>,
 *         subject?: scalar|null|Param,
 *         content_type?: scalar|null|Param, // Default: null
 *         headers?: list<scalar|null|Param>,
 *         mailer?: scalar|null|Param, // Default: null
 *         email_prototype?: string|array{
 *             id: scalar|null|Param,
 *             method?: scalar|null|Param, // Default: null
 *         },
 *         lazy?: bool|Param, // Default: true
 *         verbosity_levels?: array{
 *             VERBOSITY_QUIET?: scalar|null|Param, // Default: "ERROR"
 *             VERBOSITY_NORMAL?: scalar|null|Param, // Default: "WARNING"
 *             VERBOSITY_VERBOSE?: scalar|null|Param, // Default: "NOTICE"
 *             VERBOSITY_VERY_VERBOSE?: scalar|null|Param, // Default: "INFO"
 *             VERBOSITY_DEBUG?: scalar|null|Param, // Default: "DEBUG"
 *         },
 *         channels?: string|array{
 *             type?: scalar|null|Param,
 *             elements?: list<scalar|null|Param>,
 *         },
 *     }>,
 * }
 * @psalm-type DoctrineConfig = array{
 *     dbal?: array{
 *         default_connection?: scalar|null|Param,
 *         types?: array<string, string|array{ // Default: []
 *             class: scalar|null|Param,
 *             commented?: bool|Param, // Deprecated: The doctrine-bundle type commenting features were removed; the corresponding config parameter was deprecated in 2.0 and will be dropped in 3.0.
 *         }>,
 *         driver_schemes?: array<string, scalar|null|Param>,
 *         connections?: array<string, array{ // Default: []
 *             url?: scalar|null|Param, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *             dbname?: scalar|null|Param,
 *             host?: scalar|null|Param, // Defaults to "localhost" at runtime.
 *             port?: scalar|null|Param, // Defaults to null at runtime.
 *             user?: scalar|null|Param, // Defaults to "root" at runtime.
 *             password?: scalar|null|Param, // Defaults to null at runtime.
 *             override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *             dbname_suffix?: scalar|null|Param, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *             application_name?: scalar|null|Param,
 *             charset?: scalar|null|Param,
 *             path?: scalar|null|Param,
 *             memory?: bool|Param,
 *             unix_socket?: scalar|null|Param, // The unix socket to use for MySQL
 *             persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *             protocol?: scalar|null|Param, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *             service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *             servicename?: scalar|null|Param, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *             sessionMode?: scalar|null|Param, // The session mode to use for the oci8 driver
 *             server?: scalar|null|Param, // The name of a running database server to connect to for SQL Anywhere.
 *             default_dbname?: scalar|null|Param, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *             sslmode?: scalar|null|Param, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *             sslrootcert?: scalar|null|Param, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *             sslcert?: scalar|null|Param, // The path to the SSL client certificate file for PostgreSQL.
 *             sslkey?: scalar|null|Param, // The path to the SSL client key file for PostgreSQL.
 *             sslcrl?: scalar|null|Param, // The file name of the SSL certificate revocation list for PostgreSQL.
 *             pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *             MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *             use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *             instancename?: scalar|null|Param, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *             connectstring?: scalar|null|Param, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             driver?: scalar|null|Param, // Default: "pdo_mysql"
 *             platform_service?: scalar|null|Param, // Deprecated: The "platform_service" configuration key is deprecated since doctrine-bundle 2.9. DBAL 4 will not support setting a custom platform via connection params anymore.
 *             auto_commit?: bool|Param,
 *             schema_filter?: scalar|null|Param,
 *             logging?: bool|Param, // Default: true
 *             profiling?: bool|Param, // Default: true
 *             profiling_collect_backtrace?: bool|Param, // Enables collecting backtraces when profiling is enabled // Default: false
 *             profiling_collect_schema_errors?: bool|Param, // Enables collecting schema errors when profiling is enabled // Default: true
 *             disable_type_comments?: bool|Param,
 *             server_version?: scalar|null|Param,
 *             idle_connection_ttl?: int|Param, // Default: 600
 *             driver_class?: scalar|null|Param,
 *             wrapper_class?: scalar|null|Param,
 *             keep_slave?: bool|Param, // Deprecated: The "keep_slave" configuration key is deprecated since doctrine-bundle 2.2. Use the "keep_replica" configuration key instead.
 *             keep_replica?: bool|Param,
 *             options?: array<string, mixed>,
 *             mapping_types?: array<string, scalar|null|Param>,
 *             default_table_options?: array<string, scalar|null|Param>,
 *             schema_manager_factory?: scalar|null|Param, // Default: "doctrine.dbal.default_schema_manager_factory"
 *             result_cache?: scalar|null|Param,
 *             slaves?: array<string, array{ // Default: []
 *                 url?: scalar|null|Param, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *                 dbname?: scalar|null|Param,
 *                 host?: scalar|null|Param, // Defaults to "localhost" at runtime.
 *                 port?: scalar|null|Param, // Defaults to null at runtime.
 *                 user?: scalar|null|Param, // Defaults to "root" at runtime.
 *                 password?: scalar|null|Param, // Defaults to null at runtime.
 *                 override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *                 dbname_suffix?: scalar|null|Param, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *                 application_name?: scalar|null|Param,
 *                 charset?: scalar|null|Param,
 *                 path?: scalar|null|Param,
 *                 memory?: bool|Param,
 *                 unix_socket?: scalar|null|Param, // The unix socket to use for MySQL
 *                 persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *                 protocol?: scalar|null|Param, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *                 service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *                 servicename?: scalar|null|Param, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *                 sessionMode?: scalar|null|Param, // The session mode to use for the oci8 driver
 *                 server?: scalar|null|Param, // The name of a running database server to connect to for SQL Anywhere.
 *                 default_dbname?: scalar|null|Param, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *                 sslmode?: scalar|null|Param, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *                 sslrootcert?: scalar|null|Param, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *                 sslcert?: scalar|null|Param, // The path to the SSL client certificate file for PostgreSQL.
 *                 sslkey?: scalar|null|Param, // The path to the SSL client key file for PostgreSQL.
 *                 sslcrl?: scalar|null|Param, // The file name of the SSL certificate revocation list for PostgreSQL.
 *                 pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *                 MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *                 use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *                 instancename?: scalar|null|Param, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *                 connectstring?: scalar|null|Param, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             }>,
 *             replicas?: array<string, array{ // Default: []
 *                 url?: scalar|null|Param, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *                 dbname?: scalar|null|Param,
 *                 host?: scalar|null|Param, // Defaults to "localhost" at runtime.
 *                 port?: scalar|null|Param, // Defaults to null at runtime.
 *                 user?: scalar|null|Param, // Defaults to "root" at runtime.
 *                 password?: scalar|null|Param, // Defaults to null at runtime.
 *                 override_url?: bool|Param, // Deprecated: The "doctrine.dbal.override_url" configuration key is deprecated.
 *                 dbname_suffix?: scalar|null|Param, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *                 application_name?: scalar|null|Param,
 *                 charset?: scalar|null|Param,
 *                 path?: scalar|null|Param,
 *                 memory?: bool|Param,
 *                 unix_socket?: scalar|null|Param, // The unix socket to use for MySQL
 *                 persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *                 protocol?: scalar|null|Param, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *                 service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *                 servicename?: scalar|null|Param, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *                 sessionMode?: scalar|null|Param, // The session mode to use for the oci8 driver
 *                 server?: scalar|null|Param, // The name of a running database server to connect to for SQL Anywhere.
 *                 default_dbname?: scalar|null|Param, // Override the default database (postgres) to connect to for PostgreSQL connexion.
 *                 sslmode?: scalar|null|Param, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *                 sslrootcert?: scalar|null|Param, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *                 sslcert?: scalar|null|Param, // The path to the SSL client certificate file for PostgreSQL.
 *                 sslkey?: scalar|null|Param, // The path to the SSL client key file for PostgreSQL.
 *                 sslcrl?: scalar|null|Param, // The file name of the SSL certificate revocation list for PostgreSQL.
 *                 pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *                 MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *                 use_savepoints?: bool|Param, // Use savepoints for nested transactions
 *                 instancename?: scalar|null|Param, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *                 connectstring?: scalar|null|Param, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             }>,
 *         }>,
 *     },
 *     orm?: array{
 *         default_entity_manager?: scalar|null|Param,
 *         auto_generate_proxy_classes?: scalar|null|Param, // Auto generate mode possible values are: "NEVER", "ALWAYS", "FILE_NOT_EXISTS", "EVAL", "FILE_NOT_EXISTS_OR_CHANGED", this option is ignored when the "enable_native_lazy_objects" option is true // Default: false
 *         enable_lazy_ghost_objects?: bool|Param, // Enables the new implementation of proxies based on lazy ghosts instead of using the legacy implementation // Default: true
 *         enable_native_lazy_objects?: bool|Param, // Enables the new native implementation of PHP lazy objects instead of generated proxies // Default: false
 *         proxy_dir?: scalar|null|Param, // Configures the path where generated proxy classes are saved when using non-native lazy objects, this option is ignored when the "enable_native_lazy_objects" option is true // Default: "%kernel.build_dir%/doctrine/orm/Proxies"
 *         proxy_namespace?: scalar|null|Param, // Defines the root namespace for generated proxy classes when using non-native lazy objects, this option is ignored when the "enable_native_lazy_objects" option is true // Default: "Proxies"
 *         controller_resolver?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             auto_mapping?: bool|null|Param, // Set to false to disable using route placeholders as lookup criteria when the primary key doesn't match the argument name // Default: null
 *             evict_cache?: bool|Param, // Set to true to fetch the entity from the database instead of using the cache, if any // Default: false
 *         },
 *         entity_managers?: array<string, array{ // Default: []
 *             query_cache_driver?: string|array{
 *                 type?: scalar|null|Param, // Default: null
 *                 id?: scalar|null|Param,
 *                 pool?: scalar|null|Param,
 *             },
 *             metadata_cache_driver?: string|array{
 *                 type?: scalar|null|Param, // Default: null
 *                 id?: scalar|null|Param,
 *                 pool?: scalar|null|Param,
 *             },
 *             result_cache_driver?: string|array{
 *                 type?: scalar|null|Param, // Default: null
 *                 id?: scalar|null|Param,
 *                 pool?: scalar|null|Param,
 *             },
 *             entity_listeners?: array{
 *                 entities?: array<string, array{ // Default: []
 *                     listeners?: array<string, array{ // Default: []
 *                         events?: list<array{ // Default: []
 *                             type?: scalar|null|Param,
 *                             method?: scalar|null|Param, // Default: null
 *                         }>,
 *                     }>,
 *                 }>,
 *             },
 *             connection?: scalar|null|Param,
 *             class_metadata_factory_name?: scalar|null|Param, // Default: "Doctrine\\ORM\\Mapping\\ClassMetadataFactory"
 *             default_repository_class?: scalar|null|Param, // Default: "Doctrine\\ORM\\EntityRepository"
 *             auto_mapping?: scalar|null|Param, // Default: false
 *             naming_strategy?: scalar|null|Param, // Default: "doctrine.orm.naming_strategy.default"
 *             quote_strategy?: scalar|null|Param, // Default: "doctrine.orm.quote_strategy.default"
 *             typed_field_mapper?: scalar|null|Param, // Default: "doctrine.orm.typed_field_mapper.default"
 *             entity_listener_resolver?: scalar|null|Param, // Default: null
 *             fetch_mode_subselect_batch_size?: scalar|null|Param,
 *             repository_factory?: scalar|null|Param, // Default: "doctrine.orm.container_repository_factory"
 *             schema_ignore_classes?: list<scalar|null|Param>,
 *             report_fields_where_declared?: bool|Param, // Set to "true" to opt-in to the new mapping driver mode that was added in Doctrine ORM 2.16 and will be mandatory in ORM 3.0. See https://github.com/doctrine/orm/pull/10455. // Default: true
 *             validate_xml_mapping?: bool|Param, // Set to "true" to opt-in to the new mapping driver mode that was added in Doctrine ORM 2.14. See https://github.com/doctrine/orm/pull/6728. // Default: false
 *             second_level_cache?: array{
 *                 region_cache_driver?: string|array{
 *                     type?: scalar|null|Param, // Default: null
 *                     id?: scalar|null|Param,
 *                     pool?: scalar|null|Param,
 *                 },
 *                 region_lock_lifetime?: scalar|null|Param, // Default: 60
 *                 log_enabled?: bool|Param, // Default: true
 *                 region_lifetime?: scalar|null|Param, // Default: 3600
 *                 enabled?: bool|Param, // Default: true
 *                 factory?: scalar|null|Param,
 *                 regions?: array<string, array{ // Default: []
 *                     cache_driver?: string|array{
 *                         type?: scalar|null|Param, // Default: null
 *                         id?: scalar|null|Param,
 *                         pool?: scalar|null|Param,
 *                     },
 *                     lock_path?: scalar|null|Param, // Default: "%kernel.cache_dir%/doctrine/orm/slc/filelock"
 *                     lock_lifetime?: scalar|null|Param, // Default: 60
 *                     type?: scalar|null|Param, // Default: "default"
 *                     lifetime?: scalar|null|Param, // Default: 0
 *                     service?: scalar|null|Param,
 *                     name?: scalar|null|Param,
 *                 }>,
 *                 loggers?: array<string, array{ // Default: []
 *                     name?: scalar|null|Param,
 *                     service?: scalar|null|Param,
 *                 }>,
 *             },
 *             hydrators?: array<string, scalar|null|Param>,
 *             mappings?: array<string, bool|string|array{ // Default: []
 *                 mapping?: scalar|null|Param, // Default: true
 *                 type?: scalar|null|Param,
 *                 dir?: scalar|null|Param,
 *                 alias?: scalar|null|Param,
 *                 prefix?: scalar|null|Param,
 *                 is_bundle?: bool|Param,
 *             }>,
 *             dql?: array{
 *                 string_functions?: array<string, scalar|null|Param>,
 *                 numeric_functions?: array<string, scalar|null|Param>,
 *                 datetime_functions?: array<string, scalar|null|Param>,
 *             },
 *             filters?: array<string, string|array{ // Default: []
 *                 class: scalar|null|Param,
 *                 enabled?: bool|Param, // Default: false
 *                 parameters?: array<string, mixed>,
 *             }>,
 *             identity_generation_preferences?: array<string, scalar|null|Param>,
 *         }>,
 *         resolve_target_entities?: array<string, scalar|null|Param>,
 *     },
 * }
 * @psalm-type DoctrineMigrationsConfig = array{
 *     migrations_paths?: array<string, scalar|null|Param>,
 *     services?: array<string, scalar|null|Param>,
 *     factories?: array<string, scalar|null|Param>,
 *     storage?: array{ // Storage to use for migration status metadata.
 *         table_storage?: array{ // The default metadata storage, implemented as a table in the database.
 *             table_name?: scalar|null|Param, // Default: null
 *             version_column_name?: scalar|null|Param, // Default: null
 *             version_column_length?: scalar|null|Param, // Default: null
 *             executed_at_column_name?: scalar|null|Param, // Default: null
 *             execution_time_column_name?: scalar|null|Param, // Default: null
 *         },
 *     },
 *     migrations?: list<scalar|null|Param>,
 *     connection?: scalar|null|Param, // Connection name to use for the migrations database. // Default: null
 *     em?: scalar|null|Param, // Entity manager name to use for the migrations database (available when doctrine/orm is installed). // Default: null
 *     all_or_nothing?: scalar|null|Param, // Run all migrations in a transaction. // Default: false
 *     check_database_platform?: scalar|null|Param, // Adds an extra check in the generated migrations to allow execution only on the same platform as they were initially generated on. // Default: true
 *     custom_template?: scalar|null|Param, // Custom template path for generated migration classes. // Default: null
 *     organize_migrations?: scalar|null|Param, // Organize migrations mode. Possible values are: "BY_YEAR", "BY_YEAR_AND_MONTH", false // Default: false
 *     enable_profiler?: bool|Param, // Whether or not to enable the profiler collector to calculate and visualize migration status. This adds some queries overhead. // Default: false
 *     transactional?: bool|Param, // Whether or not to wrap migrations in a single transaction. // Default: true
 * }
 * @psalm-type CmfRoutingConfig = array{
 *     chain?: array{
 *         routers_by_id?: array<string, scalar|null|Param>,
 *         replace_symfony_router?: bool|Param, // Default: true
 *     },
 *     dynamic?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         route_collection_limit?: scalar|null|Param, // Default: 0
 *         generic_controller?: scalar|null|Param, // Default: null
 *         default_controller?: scalar|null|Param, // Default: null
 *         controllers_by_type?: array<string, scalar|null|Param>,
 *         controllers_by_class?: array<string, scalar|null|Param>,
 *         templates_by_class?: array<string, scalar|null|Param>,
 *         persistence?: array{
 *             phpcr?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 manager_name?: scalar|null|Param, // Default: null
 *                 route_basepaths?: list<scalar|null|Param>,
 *                 enable_initializer?: bool|Param, // Default: true
 *             },
 *             orm?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 manager_name?: scalar|null|Param, // Default: null
 *                 route_class?: scalar|null|Param, // Default: "Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route"
 *             },
 *         },
 *         uri_filter_regexp?: scalar|null|Param, // Default: ""
 *         route_provider_service_id?: scalar|null|Param,
 *         route_filters_by_id?: array<string, scalar|null|Param>,
 *         content_repository_service_id?: scalar|null|Param,
 *         locales?: list<scalar|null|Param>,
 *         limit_candidates?: int|Param, // Default: 20
 *         match_implicit_locale?: bool|Param, // Default: true
 *         redirectable_url_matcher?: bool|Param, // Default: false
 *         auto_locale_pattern?: bool|Param, // Default: false
 *         url_generator?: scalar|null|Param, // URL generator service ID // Default: "cmf_routing.generator"
 *     },
 * }
 * @psalm-type SchebTwoFactorConfig = array{
 *     persister?: scalar|null|Param, // Default: "scheb_two_factor.persister.doctrine"
 *     model_manager_name?: scalar|null|Param, // Default: null
 *     security_tokens?: list<scalar|null|Param>,
 *     ip_whitelist?: list<scalar|null|Param>,
 *     ip_whitelist_provider?: scalar|null|Param, // Default: "scheb_two_factor.default_ip_whitelist_provider"
 *     two_factor_token_factory?: scalar|null|Param, // Default: "scheb_two_factor.default_token_factory"
 *     two_factor_provider_decider?: scalar|null|Param, // Default: "scheb_two_factor.default_provider_decider"
 *     two_factor_condition?: scalar|null|Param, // Default: null
 *     code_reuse_cache?: scalar|null|Param, // Default: null
 *     code_reuse_cache_duration?: int|Param, // Default: 60
 *     code_reuse_default_handler?: scalar|null|Param, // Default: null
 *     google?: bool|array{
 *         enabled?: scalar|null|Param, // Default: false
 *         form_renderer?: scalar|null|Param, // Default: null
 *         issuer?: scalar|null|Param, // Default: null
 *         server_name?: scalar|null|Param, // Default: null
 *         template?: scalar|null|Param, // Default: "@SchebTwoFactor/Authentication/form.html.twig"
 *         digits?: int|Param, // Default: 6
 *         leeway?: int|Param, // Default: 0
 *     },
 * }
 * @psalm-type FosJsRoutingConfig = array{
 *     serializer?: scalar|null|Param,
 *     routes_to_expose?: list<scalar|null|Param>,
 *     router?: scalar|null|Param, // Default: "router"
 *     request_context_base_url?: scalar|null|Param, // Default: null
 *     cache_control?: array{
 *         public?: bool|Param, // Default: false
 *         expires?: scalar|null|Param, // Default: null
 *         maxage?: scalar|null|Param, // Default: null
 *         smaxage?: scalar|null|Param, // Default: null
 *         vary?: list<scalar|null|Param>,
 *     },
 * }
 * @psalm-type FlysystemConfig = array{
 *     storages?: array<string, array{ // Default: []
 *         adapter: scalar|null|Param,
 *         options?: list<mixed>,
 *         visibility?: scalar|null|Param, // Default: null
 *         directory_visibility?: scalar|null|Param, // Default: null
 *         retain_visibility?: bool|null|Param, // Default: null
 *         case_sensitive?: bool|Param, // Default: true
 *         disable_asserts?: bool|Param, // Default: false
 *         public_url?: list<scalar|null|Param>,
 *         path_normalizer?: scalar|null|Param, // Default: null
 *         public_url_generator?: scalar|null|Param, // Default: null
 *         temporary_url_generator?: scalar|null|Param, // Default: null
 *         read_only?: bool|Param, // Default: false
 *     }>,
 * }
 * @psalm-type KnpPaginatorConfig = array{
 *     default_options?: array{
 *         sort_field_name?: scalar|null|Param, // Default: "sort"
 *         sort_direction_name?: scalar|null|Param, // Default: "direction"
 *         filter_field_name?: scalar|null|Param, // Default: "filterField"
 *         filter_value_name?: scalar|null|Param, // Default: "filterValue"
 *         page_name?: scalar|null|Param, // Default: "page"
 *         distinct?: bool|Param, // Default: true
 *         page_out_of_range?: scalar|null|Param, // Default: "ignore"
 *         default_limit?: scalar|null|Param, // Default: 10
 *     },
 *     template?: array{
 *         pagination?: scalar|null|Param, // Default: "@KnpPaginator/Pagination/sliding.html.twig"
 *         rel_links?: scalar|null|Param, // Default: "@KnpPaginator/Pagination/rel_links.html.twig"
 *         filtration?: scalar|null|Param, // Default: "@KnpPaginator/Pagination/filtration.html.twig"
 *         sortable?: scalar|null|Param, // Default: "@KnpPaginator/Pagination/sortable_link.html.twig"
 *     },
 *     page_range?: scalar|null|Param, // Default: 5
 *     page_limit?: scalar|null|Param, // Default: null
 *     convert_exception?: bool|Param, // Default: false
 *     remove_first_page_param?: bool|Param, // Default: false
 * }
 * @psalm-type CoreShopCoreConfig = array{
 *     send_usage_log?: scalar|null|Param, // Default: true
 *     checkout_manager_factory?: scalar|null|Param,
 *     after_logout_redirect_route?: scalar|null|Param, // Default: "coreshop_index"
 *     autoconfigure_with_attributes?: scalar|null|Param, // Default: false
 *     resources?: array{
 *         product_store_values?: array{
 *             options?: mixed,
 *             graphql?: array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             classes?: array{
 *                 model?: scalar|null|Param, // Default: "CoreShop\\Component\\Core\\Model\\ProductStoreValues"
 *                 interface?: scalar|null|Param, // Default: "CoreShop\\Component\\Core\\Model\\ProductStoreValuesInterface"
 *                 factory?: scalar|null|Param, // Default: "CoreShop\\Component\\Resource\\Factory\\Factory"
 *                 repository?: scalar|null|Param, // Default: "CoreShop\\Bundle\\CoreBundle\\Doctrine\\ORM\\ProductStoreValuesRepository"
 *             },
 *         },
 *     },
 *     pimcore_admin?: array{
 *         js?: array<string, scalar|null|Param>,
 *         css?: array{
 *             core?: scalar|null|Param, // Default: "/bundles/coreshopcore/pimcore/css/core.css"
 *             ...<mixed>
 *         },
 *         permissions?: scalar|null|Param, // Default: ["settings","ctc_assign_to_new","ctc_assign_to_existing"]
 *         editmode_js?: array<string, scalar|null|Param>,
 *         editmode_css?: array<string, scalar|null|Param>,
 *     },
 *     checkout: array<string, array{ // Default: []
 *         steps?: array<string, array{ // Default: []
 *             step: scalar|null|Param,
 *             priority: int|Param,
 *         }>,
 *     }>,
 * }
 * @psalm-type CoreShopStorageListConfig = array{
 *     list?: array<string, array{ // Default: []
 *         disable_caching?: bool|Param, // Default: false
 *         context?: array{
 *             interface?: scalar|null|Param, // Default: "CoreShop\\Component\\StorageList\\Context\\StorageListContextInterface"
 *             composite?: scalar|null|Param, // Default: "CoreShop\\Component\\StorageList\\Context\\CompositeStorageListContext"
 *             tag?: scalar|null|Param,
 *             restore_customer_list_only_on_login?: bool|Param, // Default: false
 *         },
 *         services?: array{
 *             manager?: scalar|null|Param, // Default: "CoreShop\\Component\\StorageList\\SessionStorageManager"
 *             modifier?: scalar|null|Param, // Default: "CoreShop\\Component\\StorageList\\StorageListModifierInterface"
 *             enable_default_store_based_decorator?: scalar|null|Param, // Default: false
 *             list_resolver?: scalar|null|Param, // Default: null
 *         },
 *         resource?: array{
 *             interface: scalar|null|Param,
 *             product_repository: scalar|null|Param,
 *             repository: scalar|null|Param,
 *             item_repository: scalar|null|Param,
 *             factory: scalar|null|Param,
 *             item_factory: scalar|null|Param,
 *             add_to_list_factory?: scalar|null|Param, // Default: "CoreShop\\Component\\StorageList\\Factory\\AddToStorageListFactory"
 *         },
 *         form?: array{
 *             type?: scalar|null|Param,
 *             add_type?: scalar|null|Param,
 *         },
 *         routes?: array{
 *             summary?: scalar|null|Param,
 *             index?: scalar|null|Param,
 *         },
 *         templates?: array{
 *             add_to_cart?: scalar|null|Param,
 *             summary?: scalar|null|Param,
 *         },
 *         session?: array{
 *             enabled?: bool|Param, // Default: false
 *             enable_logout_subscriber?: bool|Param, // Default: false
 *             key?: scalar|null|Param, // Default: "storage_list"
 *         },
 *         controller?: array{
 *             enabled?: bool|Param, // Default: false
 *             class?: scalar|null|Param, // Default: "CoreShop\\Bundle\\StorageListBundle\\Controller\\StorageListController"
 *         },
 *         multi_list?: array{
 *             enabled?: bool|Param, // Default: false
 *             controller?: array{
 *                 enabled?: bool|Param, // Default: false
 *                 class?: scalar|null|Param, // Default: "CoreShop\\Bundle\\StorageListBundle\\Controller\\StorageMultiListController"
 *             },
 *             templates?: array{
 *                 create_new_storage_list?: scalar|null|Param,
 *                 list_storage_list?: scalar|null|Param,
 *             },
 *             form?: array{
 *                 class?: scalar|null|Param,
 *             },
 *         },
 *         expiration?: array{
 *             enabled?: bool|Param, // Default: false
 *             service?: scalar|null|Param, // Default: null
 *             days?: int|Param, // Default: 14
 *             params?: mixed, // Default: []
 *         },
 *     }>,
 * }
 * @psalm-type DebugConfig = array{
 *     max_items?: int|Param, // Max number of displayed items past the first level, -1 means no limit. // Default: 2500
 *     min_depth?: int|Param, // Minimum tree depth to clone all the items, 1 is default. // Default: 1
 *     max_string_length?: int|Param, // Max length of displayed strings, -1 means no limit. // Default: -1
 *     dump_destination?: scalar|null|Param, // A stream URL where dumps should be written to. // Default: null
 *     theme?: "dark"|"light"|Param, // Changes the color of the dump() output when rendered directly on the templating. "dark" (default) or "light". // Default: "dark"
 * }
 * @psalm-type WebProfilerConfig = array{
 *     toolbar?: bool|array{ // Profiler toolbar configuration
 *         enabled?: bool|Param, // Default: false
 *         ajax_replace?: bool|Param, // Replace toolbar on AJAX requests // Default: false
 *     },
 *     intercept_redirects?: bool|Param, // Default: false
 *     excluded_ajax_paths?: scalar|null|Param, // Default: "^/((index|app(_[\\w]+)?)\\.php/)?_wdt"
 * }
 * @psalm-type PrestaSitemapConfig = array{
 *     generator?: scalar|null|Param, // Default: "presta_sitemap.generator_default"
 *     dumper?: scalar|null|Param, // Default: "presta_sitemap.dumper_default"
 *     timetolive?: int|Param, // Default: 3600
 *     sitemap_file_prefix?: scalar|null|Param, // Sets sitemap filename prefix defaults to "sitemap" -> sitemap.xml (for index); sitemap.<section>.xml(.gz) (for sitemaps) // Default: "sitemap"
 *     items_by_set?: int|Param, // The maximum number of items allowed in single sitemap. // Default: 50000
 *     route_annotation_listener?: scalar|null|Param, // Default: true
 *     dump_directory?: scalar|null|Param, // The directory to which the sitemap will be dumped. It can be either absolute, or relative (to the place where the command will be triggered). Default to Symfony's public dir. // Default: "%kernel.project_dir%/public"
 *     defaults?: array{
 *         priority?: scalar|null|Param, // Default: 0.5
 *         changefreq?: scalar|null|Param, // Default: "daily"
 *         lastmod?: scalar|null|Param, // Default: "now"
 *     },
 *     default_section?: scalar|null|Param, // The default section in which static routes are registered. // Default: "default"
 *     alternate?: bool|array{ // Automatically generate alternate (hreflang) urls with static routes. Requires route_annotation_listener config to be enabled.
 *         enabled?: bool|Param, // Default: false
 *         default_locale?: scalar|null|Param, // The default locale of your routes. // Default: "en"
 *         locales?: list<scalar|null|Param>,
 *         i18n?: "symfony"|"jms"|Param, // Strategy used to create your i18n routes. // Default: "symfony"
 *     },
 * }
 * @psalm-type PimcoreStaticResolverConfig = array<mixed>
 * @psalm-type PimcoreElasticsearchClientConfig = array{
 *     es_clients?: array<string, array{ // Default: []
 *         name?: scalar|null|Param,
 *         hosts?: list<scalar|null|Param>,
 *         logger_channel?: scalar|null|Param, // Logger channel to be used for elasticsearch client logs // Default: "pimcore.elasticsearch.default"
 *         username?: scalar|null|Param, // Username for elasticsearch authentication
 *         password?: scalar|null|Param, // Password for elasticsearch authentication
 *         ca_bundle?: scalar|null|Param, // Path to certificate authority file (.crt)
 *         ssl_key?: scalar|null|Param, // Path to private SSL key file (.key)
 *         ssl_cert?: scalar|null|Param, // Path to PEM formatted SSL cert file (.cert)
 *         ssl_password?: scalar|null|Param, // If private key and certificate require a password (default: null)
 *         ssl_verification?: bool|Param, // Enable or disable the SSL verification (default: true)
 *         http_options?: list<scalar|null|Param>,
 *         cloud_id?: scalar|null|Param, // Elastic Cloud Id for elasticsearch cloud authentication
 *         api_key?: scalar|null|Param, // Elastic Cloud API key for elasticsearch cloud authentication
 *     }>,
 * }
 * @psalm-type MercureConfig = array{
 *     hubs?: array<string, array{ // Default: []
 *         url?: scalar|null|Param, // URL of the hub's publish endpoint
 *         public_url?: scalar|null|Param, // URL of the hub's public endpoint // Default: null
 *         jwt?: string|array{ // JSON Web Token configuration.
 *             value?: scalar|null|Param, // JSON Web Token to use to publish to this hub.
 *             provider?: scalar|null|Param, // The ID of a service to call to provide the JSON Web Token.
 *             factory?: scalar|null|Param, // The ID of a service to call to create the JSON Web Token.
 *             publish?: list<scalar|null|Param>,
 *             subscribe?: list<scalar|null|Param>,
 *             secret?: scalar|null|Param, // The JWT Secret to use.
 *             passphrase?: scalar|null|Param, // The JWT secret passphrase. // Default: ""
 *             algorithm?: scalar|null|Param, // The algorithm to use to sign the JWT // Default: "hmac.sha256"
 *         },
 *         jwt_provider?: scalar|null|Param, // Deprecated: The child node "jwt_provider" at path "mercure.hubs..jwt_provider" is deprecated, use "jwt.provider" instead. // The ID of a service to call to generate the JSON Web Token.
 *         bus?: scalar|null|Param, // Name of the Messenger bus where the handler for this hub must be registered. Default to the default bus if Messenger is enabled.
 *     }>,
 *     default_hub?: scalar|null|Param,
 *     default_cookie_lifetime?: int|Param, // Default lifetime of the cookie containing the JWT, in seconds. Defaults to the value of "framework.session.cookie_lifetime". // Default: null
 *     enable_profiler?: bool|Param, // Deprecated: The child node "enable_profiler" at path "mercure.enable_profiler" is deprecated. // Enable Symfony Web Profiler integration.
 * }
 * @psalm-type WebpackEncoreConfig = array{
 *     output_path: scalar|null|Param, // The path where Encore is building the assets - i.e. Encore.setOutputPath()
 *     crossorigin?: false|"anonymous"|"use-credentials"|Param, // crossorigin value when Encore.enableIntegrityHashes() is used, can be false (default), anonymous or use-credentials // Default: false
 *     preload?: bool|Param, // preload all rendered script and link tags automatically via the http2 Link header. // Default: false
 *     cache?: bool|Param, // Enable caching of the entry point file(s) // Default: false
 *     strict_mode?: bool|Param, // Throw an exception if the entrypoints.json file is missing or an entry is missing from the data // Default: true
 *     builds?: array<string, scalar|null|Param>,
 *     script_attributes?: array<string, scalar|null|Param>,
 *     link_attributes?: array<string, scalar|null|Param>,
 * }
 * @psalm-type KnpMenuConfig = array{
 *     providers?: array{
 *         builder_alias?: bool|Param, // Default: true
 *     },
 *     twig?: array{
 *         template?: scalar|null|Param, // Default: "@KnpMenu/menu.html.twig"
 *     },
 *     templating?: bool|Param, // Default: false
 *     default_renderer?: scalar|null|Param, // Default: "twig"
 * }
 * @psalm-type PimcoreConfig = array{
 *     bundles?: array{ // Define parameters for Pimcore Bundle Locator
 *         search_paths?: list<scalar|null|Param>,
 *         handle_composer?: bool|Param, // Define whether it should be scanning bundles through composer /vendor folder or not // Default: true
 *     },
 *     flags?: list<scalar|null|Param>,
 *     translations?: array{
 *         domains?: list<scalar|null|Param>,
 *         admin_translation_mapping?: array<string, scalar|null|Param>,
 *         debugging?: bool|array{ // If debugging is enabled, the translator will return the plain translation key instead of the translated message.
 *             enabled?: bool|Param, // Default: true
 *             parameter?: scalar|null|Param, // Default: "pimcore_debug_translations"
 *         },
 *     },
 *     maps?: array{
 *         tile_layer_url_template?: scalar|null|Param, // Default: "https://a.tile.openstreetmap.org/{z}/{x}/{y}.png"
 *         geocoding_url_template?: scalar|null|Param, // Default: "https://nominatim.openstreetmap.org/search?q={q}&addressdetails=1&format=json&limit=1"
 *         reverse_geocoding_url_template?: scalar|null|Param, // Default: "https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&addressdetails=1"
 *     },
 *     general?: array{
 *         timezone?: scalar|null|Param, // Default: ""
 *         path_variable?: scalar|null|Param, // Additional $PATH variable (: separated) (/x/y:/foo/bar): // Default: null
 *         domain?: scalar|null|Param, // Default: ""
 *         redirect_to_maindomain?: bool|Param, // Default: false
 *         language?: scalar|null|Param, // Deprecated: The child node "language" at path "pimcore.general.language" is deprecated. // Default: "en"
 *         valid_languages?: list<scalar|null|Param>,
 *         required_languages?: list<scalar|null|Param>,
 *         fallback_languages?: list<scalar|null|Param>,
 *         default_language?: scalar|null|Param, // Default: "en"
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
 *             days?: scalar|null|Param, // Default: null
 *             steps?: scalar|null|Param, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         custom_layouts?: array{
 *             definitions?: list<array{ // Default: []
 *                 id?: scalar|null|Param,
 *                 name?: scalar|null|Param,
 *                 description?: scalar|null|Param, // Default: null
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *                 userOwner?: int|Param,
 *                 userModification?: int|Param,
 *                 classId?: scalar|null|Param,
 *                 default?: int|Param,
 *                 layoutDefinitions?: mixed,
 *             }>,
 *         },
 *         select_options?: array{
 *             definitions?: list<array{ // Default: []
 *                 id?: scalar|null|Param,
 *                 group?: scalar|null|Param,
 *                 adminOnly?: bool|Param, // Default: false
 *                 useTraits?: scalar|null|Param,
 *                 implementsInterfaces?: scalar|null|Param,
 *                 selectOptions?: list<array{ // Default: []
 *                     value?: scalar|null|Param,
 *                     label?: scalar|null|Param,
 *                     name?: scalar|null|Param,
 *                 }>,
 *             }>,
 *         },
 *         class_definitions?: array{
 *             data?: array{
 *                 map?: array<string, scalar|null|Param>,
 *                 prefixes?: list<scalar|null|Param>,
 *             },
 *             layout?: array{
 *                 map?: array<string, scalar|null|Param>,
 *                 prefixes?: list<scalar|null|Param>,
 *             },
 *         },
 *         ...<mixed>
 *     },
 *     assets?: array{
 *         thumbnails?: array{
 *             allowed_formats?: list<scalar|null|Param>,
 *             max_scaling_factor?: float|Param, // Default: 5.0
 *         },
 *         frontend_prefixes?: array{
 *             source?: scalar|null|Param, // Default: ""
 *             thumbnail?: scalar|null|Param, // Default: ""
 *             thumbnail_deferred?: scalar|null|Param, // Default: ""
 *         },
 *         preview_image_thumbnail?: scalar|null|Param, // Default: null
 *         default_upload_path?: scalar|null|Param, // Default: "_default_upload_bucket"
 *         tree_paging_limit?: int|Param, // Default: 100
 *         image?: array{
 *             max_pixels?: int|Param, // Maximum number of pixels an image can have when added (width × height). // Default: 40000000
 *             low_quality_image_preview?: bool|array{ // Allow a LQIP SVG image to be generated alongside any other thumbnails.
 *                 enabled?: bool|Param, // Default: true
 *             },
 *             thumbnails?: array{
 *                 definitions?: list<array{ // Default: []
 *                     id?: scalar|null|Param,
 *                     name?: scalar|null|Param,
 *                     description?: scalar|null|Param,
 *                     group?: scalar|null|Param,
 *                     format?: scalar|null|Param,
 *                     quality?: scalar|null|Param,
 *                     highResolution?: scalar|null|Param,
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
 *                         method?: scalar|null|Param,
 *                         arguments?: list<mixed>,
 *                     }>,
 *                     medias?: list<list<array{ // Default: []
 *                             method?: scalar|null|Param,
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
 *                     quality?: scalar|null|Param,
 *                 }>,
 *                 status_cache?: bool|Param, // Store image metadata such as filename and modification date in assets_image_thumbnail_cache, this is helpful when using remote object storage for thumbnails. // Default: true
 *                 auto_clear_temp_files?: bool|Param, // Automatically delete all image thumbnail files any time an image or its metadata is updated. // Default: true
 *             },
 *         },
 *         video?: array{
 *             thumbnails?: array{
 *                 definitions?: list<array{ // Default: []
 *                     id?: scalar|null|Param,
 *                     name?: scalar|null|Param,
 *                     description?: scalar|null|Param,
 *                     group?: scalar|null|Param,
 *                     videoBitrate?: scalar|null|Param,
 *                     audioBitrate?: scalar|null|Param,
 *                     quality?: scalar|null|Param,
 *                     modificationDate?: int|Param,
 *                     creationDate?: int|Param,
 *                     items?: list<array{ // Default: []
 *                         method?: scalar|null|Param,
 *                         arguments?: list<mixed>,
 *                     }>,
 *                     medias?: list<list<array{ // Default: []
 *                             method?: scalar|null|Param,
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
 *             days?: scalar|null|Param, // Default: null
 *             steps?: scalar|null|Param, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             use_hardlinks?: bool|Param, // Default: true
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         icc_rgb_profile?: scalar|null|Param, // Absolute path to default ICC RGB profile (if no embedded profile is given) // Default: null
 *         icc_cmyk_profile?: scalar|null|Param, // Absolute path to default ICC CMYK profile (if no embedded profile is given) // Default: null
 *         metadata?: array{
 *             alt?: scalar|null|Param, // Set to replace the default metadata used for auto alt functionality in frontend // Default: ""
 *             copyright?: scalar|null|Param, // Set to replace the default metadata used for copyright in frontend // Default: ""
 *             title?: scalar|null|Param, // Set to replace the default metadata used for title in frontend // Default: ""
 *             predefined?: array{
 *                 definitions?: list<array{ // Default: []
 *                     name?: scalar|null|Param,
 *                     description?: scalar|null|Param,
 *                     group?: scalar|null|Param,
 *                     language?: scalar|null|Param,
 *                     type?: scalar|null|Param,
 *                     data?: scalar|null|Param,
 *                     targetSubtype?: scalar|null|Param,
 *                     config?: scalar|null|Param,
 *                     inheritable?: bool|Param,
 *                     creationDate?: int|Param,
 *                     modificationDate?: int|Param,
 *                 }>,
 *             },
 *             class_definitions?: array{
 *                 data?: array{
 *                     map?: array<string, scalar|null|Param>,
 *                     prefixes?: list<scalar|null|Param>,
 *                 },
 *             },
 *         },
 *         type_definitions?: array{
 *             map?: list<array{ // Default: []
 *                 class?: scalar|null|Param,
 *                 matching?: list<scalar|null|Param>,
 *             }>,
 *         },
 *     },
 *     documents?: array{
 *         doc_types?: array{
 *             definitions?: list<array{ // Default: []
 *                 name?: scalar|null|Param,
 *                 group?: scalar|null|Param,
 *                 module?: scalar|null|Param,
 *                 controller?: scalar|null|Param,
 *                 template?: scalar|null|Param,
 *                 type?: scalar|null|Param,
 *                 priority?: int|Param,
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *                 staticGeneratorEnabled?: bool|Param, // Default: false
 *             }>,
 *         },
 *         versions?: array{
 *             days?: scalar|null|Param, // Default: null
 *             steps?: scalar|null|Param, // Default: null
 *             disable_events?: bool|Param, // Default: false
 *             disable_stack_trace?: bool|Param, // Default: false
 *         },
 *         default_controller?: scalar|null|Param, // Default: "App\\Controller\\DefaultController::defaultAction"
 *         error_pages?: array{
 *             default?: scalar|null|Param, // Default: null
 *             localized?: list<scalar|null|Param>,
 *         },
 *         allow_trailing_slash?: scalar|null|Param, // Default: "no"
 *         generate_preview?: bool|Param, // Default: false
 *         preview_url_prefix?: scalar|null|Param, // Default: ""
 *         tree_paging_limit?: int|Param, // Default: 50
 *         editables?: array{
 *             map?: array<string, scalar|null|Param>,
 *             prefixes?: list<scalar|null|Param>,
 *         },
 *         areas?: array{
 *             autoload?: bool|Param, // Default: true
 *         },
 *         auto_save_interval?: int|Param, // Default: 60
 *         static_page_router?: array{
 *             enabled?: bool|Param, // Enable Static Page router for document when using remote storage for generated pages // Default: false
 *             route_pattern?: scalar|null|Param, // Optionally define route patterns to lookup static pages. Regular Expressions like: /^\/en\/Magazine/ // Default: null
 *         },
 *         static_page_generator?: array{
 *             use_main_domain?: bool|Param, // Use main domain for static pages folder in tmp/pages // Default: false
 *             headers?: list<array{ // Default: []
 *                 name?: scalar|null|Param,
 *                 value?: scalar|null|Param,
 *             }>,
 *         },
 *         type_definitions?: array{
 *             map?: list<array{ // Default: []
 *                 class?: scalar|null|Param,
 *                 translatable?: bool|Param, // Default: true
 *                 valid_table?: scalar|null|Param, // Default: null
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
 *         secret?: scalar|null|Param, // Default: null
 *     },
 *     models?: array{
 *         class_overrides?: array<string, scalar|null|Param>,
 *     },
 *     routing?: array{
 *         static?: array{
 *             locale_params?: list<scalar|null|Param>,
 *         },
 *     },
 *     full_page_cache?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         lifetime?: scalar|null|Param, // Optional output-cache lifetime (in seconds) after the cache expires, if not defined the cache will be cleaned on every action inside the CMS, otherwise not (for high traffic sites) // Default: null
 *         exclude_patterns?: scalar|null|Param, // Regular Expressions like: /^\/dir\/toexclude/
 *         exclude_cookie?: scalar|null|Param, // Comma separated list of cookie names, that will automatically disable the full-page cache
 *         ...<mixed>
 *     },
 *     context?: array<string, array{ // Default: []
 *         routes?: list<null|array{ // Default: []
 *             path?: scalar|null|Param, // Default: false
 *             route?: scalar|null|Param, // Default: false
 *             host?: scalar|null|Param, // Default: false
 *             methods?: list<scalar|null|Param>,
 *         }>,
 *     }>,
 *     web_profiler?: array{
 *         toolbar?: array{
 *             excluded_routes?: list<null|array{ // Default: []
 *                 path?: scalar|null|Param, // Default: false
 *                 route?: scalar|null|Param, // Default: false
 *                 host?: scalar|null|Param, // Default: false
 *                 methods?: list<scalar|null|Param>,
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
 *             id?: scalar|null|Param,
 *         }>,
 *         password_hasher_factories?: array<string, string|array{ // Default: []
 *             id?: scalar|null|Param,
 *         }>,
 *     },
 *     email?: array{
 *         sender?: array{
 *             name?: scalar|null|Param, // Default: ""
 *             email?: scalar|null|Param, // Default: ""
 *         },
 *         return?: array{
 *             name?: scalar|null|Param, // Default: ""
 *             email?: scalar|null|Param, // Default: ""
 *         },
 *         debug?: array{
 *             email_addresses?: scalar|null|Param, // Default: ""
 *         },
 *         usespecific?: scalar|null|Param, // Default: false
 *     },
 *     workflows?: array<string, array{ // Default: []
 *         placeholders?: list<scalar|null|Param>,
 *         custom_extensions?: array<mixed>,
 *         enabled?: bool|Param, // Can be used to enable or disable the workflow. // Default: true
 *         priority?: int|Param, // When multiple custom view or permission settings from different places in different workflows are valid, the workflow with the highest priority will be used. // Default: 0
 *         label?: scalar|null|Param, // Will be used in the backend interface as nice name for the workflow. If not set the technical workflow name will be used as label too.
 *         audit_trail?: bool|array{ // Enable default audit trail feature provided by Symfony. Take a look at the Symfony docs for more details.
 *             enabled?: bool|Param, // Default: false
 *         },
 *         type?: "workflow"|"state_machine"|Param, // A workflow with type "workflow" can handle multiple places at one time whereas a state_machine provides a finite state_machine (only one place at one time). Take a look at the Symfony docs for more details.
 *         marking_store?: array{ // Handles the way how the state/place is stored. If not defined "state_table" will be used as default. Take a look at @TODO for a description of the different types.
 *             type?: "multiple_state"|"single_state"|"state_table"|"data_object_multiple_state"|"data_object_splitted_state"|Param,
 *             arguments?: list<mixed>,
 *             service?: scalar|null|Param,
 *         },
 *         supports?: list<scalar|null|Param>,
 *         support_strategy?: array{ // Can be used to implement a special logic which subjects are supported by the workflow. For example only products matching certain criteria.
 *             type?: "expression"|Param, // Type "expression": a symfony expression to define a criteria.
 *             arguments?: list<mixed>,
 *             service?: scalar|null|Param, // Define a custom service to handle the logic. Take a look at the Symfony docs for more details.
 *         },
 *         initial_markings?: list<scalar|null|Param>,
 *         places?: list<array{ // Default: []
 *             label?: scalar|null|Param, // Nice name which will be used in the Pimcore backend.
 *             title?: scalar|null|Param, // Title/tooltip for this place when it is displayed in the header of the Pimcore element detail view in the backend. // Default: ""
 *             color?: scalar|null|Param, // Color of the place which will be used in the Pimcore backend. // Default: "#bfdadc"
 *             colorInverted?: bool|Param, // If set to true the color will be used as border and font color otherwise as background color. // Default: false
 *             visibleInHeader?: bool|Param, // If set to false, the place will be hidden in the header of the Pimcore element detail view in the backend. // Default: true
 *             permissions?: list<array{ // Default: []
 *                 condition?: scalar|null|Param, // A symfony expression can be configured here. The first set of permissions which are matching the condition will be used.
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
 *                 objectLayout?: scalar|null|Param, // if set, the user will see the configured custom data object layout
 *             }>,
 *         }>,
 *         transitions: list<array{ // Default: []
 *             name: scalar|null|Param,
 *             guard?: scalar|null|Param, // An expression to block the transition
 *             from?: list<scalar|null|Param>,
 *             to?: list<scalar|null|Param>,
 *             options?: array{
 *                 label?: scalar|null|Param, // Nice name for the Pimcore backend.
 *                 notes?: array{
 *                     commentEnabled?: bool|Param, // If enabled a detail window will open when the user executes the transition. In this detail view the user be asked to enter a "comment". This comment then will be used as comment for the notes/events feature. // Default: false
 *                     commentRequired?: bool|Param, // Set this to true if the comment should be a required field. // Default: false
 *                     commentSetterFn?: scalar|null|Param, // Can be used for data objects. The comment will be saved to the data object additionally to the notes/events through this setter function.
 *                     commentGetterFn?: scalar|null|Param, // Can be used for data objects to prefill the comment field with data from the data object.
 *                     type?: scalar|null|Param, // Set's the type string in the saved note. // Default: "Status update"
 *                     title?: scalar|null|Param, // An optional alternative "title" for the note, if blank the actions transition result is used.
 *                     additionalFields?: list<array{ // Default: []
 *                         name: scalar|null|Param, // The technical name used in the input form.
 *                         fieldType: "input"|"numeric"|"textarea"|"select"|"datetime"|"date"|"user"|"checkbox"|Param, // The data component name/field type.
 *                         title?: scalar|null|Param, // The label used by the field
 *                         required?: bool|Param, // Whether or not the field is required. // Default: false
 *                         setterFn?: scalar|null|Param, // Optional setter function (available in the element, for example in the updated object), if not specified, data will be added to notes. The Workflow manager will call the function with the whole field data.
 *                         fieldTypeSettings?: list<mixed>,
 *                     }>,
 *                     customHtml?: array{
 *                         position?: "top"|"center"|"bottom"|Param, // Set position of custom HTML inside modal (top, center, bottom). // Default: "top"
 *                         service?: scalar|null|Param, // Define a custom service for rendering custom HTML within the note modal.
 *                     },
 *                 },
 *                 iconClass?: scalar|null|Param, // CSS class to define the icon which will be used in the actions button in the backend.
 *                 objectLayout?: scalar|null|Param, // Forces an object layout after the transition was performed. This objectLayout setting overrules all objectLayout settings within the places configs. // Default: false
 *                 notificationSettings?: list<array{ // Default: []
 *                     condition?: scalar|null|Param, // A symfony expression can be configured here. All sets of notification which are matching the condition will be used.
 *                     notifyUsers?: list<scalar|null|Param>,
 *                     notifyRoles?: list<scalar|null|Param>,
 *                     channelType?: list<"mail"|"pimcore_notification"|Param>,
 *                     mailType?: "template"|"pimcore_document"|Param, // Type of mail source. // Default: "template"
 *                     mailPath?: scalar|null|Param, // Path to mail source - either Symfony path to template or fullpath to Pimcore document. Optional use %_locale% as placeholder for language. // Default: "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig"
 *                 }>,
 *                 changePublishedState?: "no_change"|"force_unpublished"|"force_published"|"save_version"|Param, // Change published state of element while transition (only available for documents and data objects). // Default: "no_change"
 *                 unsavedChangesBehaviour?: "save"|"warn"|"ignore"|Param, // Behaviour when workflow transition gets applied but there are unsaved changes // Default: "warn"
 *             },
 *         }>,
 *         globalActions?: list<array{ // Default: []
 *             label?: scalar|null|Param, // Nice name for the Pimcore backend.
 *             iconClass?: scalar|null|Param, // CSS class to define the icon which will be used in the actions button in the backend.
 *             objectLayout?: scalar|null|Param, // Forces an object layout after the global action was performed. This objectLayout setting overrules all objectLayout settings within the places configs. // Default: false
 *             guard?: scalar|null|Param, // An expression to block the action
 *             saveSubject?: bool|Param, // Determines if the global action should perform a save on the subject, default behavior is set to true // Default: true
 *             to?: list<scalar|null|Param>,
 *             notes?: array{ // See notes section of transitions. It works exactly the same way.
 *                 commentEnabled?: bool|Param, // Default: false
 *                 commentRequired?: bool|Param, // Default: false
 *                 commentSetterFn?: scalar|null|Param,
 *                 commentGetterFn?: scalar|null|Param,
 *                 type?: scalar|null|Param, // Default: "Status update"
 *                 title?: scalar|null|Param,
 *                 additionalFields?: list<array{ // Default: []
 *                     name: scalar|null|Param,
 *                     fieldType: "input"|"textarea"|"select"|"datetime"|"date"|"user"|"checkbox"|Param,
 *                     title?: scalar|null|Param,
 *                     required?: bool|Param, // Default: false
 *                     setterFn?: scalar|null|Param,
 *                     fieldTypeSettings?: list<mixed>,
 *                 }>,
 *                 customHtml?: array{
 *                     position?: "top"|"center"|"bottom"|Param, // Set position of custom HTML inside modal (top, center, bottom). // Default: "top"
 *                     service?: scalar|null|Param, // Define a custom service for rendering custom HTML within the note modal.
 *                 },
 *             },
 *         }>,
 *     }>,
 *     httpclient?: array{
 *         adapter?: scalar|null|Param, // Set to `Proxy` if proxy server should be used // Default: "Socket"
 *         proxy_host?: scalar|null|Param, // Default: null
 *         proxy_port?: scalar|null|Param, // Default: null
 *         proxy_user?: scalar|null|Param, // Default: null
 *         proxy_pass?: scalar|null|Param, // Default: null
 *     },
 *     applicationlog?: array{
 *         loggers?: array{
 *             db?: array{
 *                 min_level_or_list?: mixed, // Default: "debug"
 *                 max_level?: scalar|null|Param, // Default: "emergency"
 *             },
 *         },
 *         mail_notification?: array{
 *             send_log_summary?: bool|Param, // Send log summary via email // Default: false
 *             filter_priority?: scalar|null|Param, // Filter threshold for email summary, choose one of: 8 (debug),7 (info),6 (notice),5 (warning),4 (error),3 (critical),2 (alert),1 (emerg). You can use the integer or the string representation. // Default: null
 *             mail_receiver?: scalar|null|Param, // Log summary receivers. Separate multiple email receivers by using ;
 *         },
 *         archive_treshold?: scalar|null|Param, // Archive threshold in days // Default: 30
 *         archive_alternative_database?: scalar|null|Param, // Archive database name (optional). Tables will get archived to a different database, recommended when huge amounts of logs will be generated // Default: ""
 *         archive_db_table_storage_engine?: scalar|null|Param, // DB storage engine to be used for archive tables (e.g. ARCHIVE, InnoDB, Aria, ...) // Default: "archive"
 *         delete_archive_threshold?: scalar|null|Param, // Threshold for deleting application log archive tables (in months) // Default: "6"
 *     },
 *     properties?: array{
 *         predefined?: array{
 *             definitions?: list<array{ // Default: []
 *                 name?: scalar|null|Param,
 *                 description?: scalar|null|Param,
 *                 key?: scalar|null|Param,
 *                 type?: scalar|null|Param,
 *                 data?: scalar|null|Param,
 *                 config?: scalar|null|Param,
 *                 ctype?: scalar|null|Param,
 *                 inheritable?: bool|Param,
 *                 creationDate?: int|Param,
 *                 modificationDate?: int|Param,
 *             }>,
 *         },
 *         ...<mixed>
 *     },
 *     perspectives?: array{
 *         definitions?: list<array{ // Default: []
 *             iconCls?: scalar|null|Param,
 *             icon?: scalar|null|Param,
 *             toolbar?: mixed,
 *             dashboards?: array{
 *                 disabledPortlets?: mixed,
 *                 predefined?: mixed,
 *             },
 *             elementTree?: list<array{ // Default: []
 *                 type?: scalar|null|Param,
 *                 position?: scalar|null|Param,
 *                 name?: scalar|null|Param,
 *                 expanded?: bool|Param,
 *                 hidden?: scalar|null|Param,
 *                 sort?: int|Param,
 *                 id?: scalar|null|Param,
 *                 treeContextMenu?: mixed,
 *             }>,
 *         }>,
 *         ...<mixed>
 *     },
 *     custom_views?: array{
 *         definitions?: list<array{ // Default: []
 *             id?: scalar|null|Param,
 *             treetype?: scalar|null|Param,
 *             name?: scalar|null|Param,
 *             condition?: scalar|null|Param,
 *             icon?: scalar|null|Param,
 *             rootfolder?: scalar|null|Param,
 *             showroot?: scalar|null|Param,
 *             classes?: mixed,
 *             position?: scalar|null|Param,
 *             sort?: scalar|null|Param,
 *             expanded?: bool|Param,
 *             having?: scalar|null|Param,
 *             where?: scalar|null|Param,
 *             treeContextMenu?: mixed,
 *             joins?: list<array{ // Default: []
 *                 type?: scalar|null|Param,
 *                 condition?: scalar|null|Param,
 *                 name?: mixed,
 *                 columns?: mixed,
 *             }>,
 *         }>,
 *         ...<mixed>
 *     },
 *     templating_engine?: array{
 *         twig?: array{
 *             sandbox_security_policy?: array{ // Allowlist tags, filters & functions for evaluating twig templates in a sandbox environment e.g. used by Mailer & Text layout component.
 *                 tags?: list<scalar|null|Param>,
 *                 filters?: list<scalar|null|Param>,
 *                 functions?: list<scalar|null|Param>,
 *             },
 *         },
 *     },
 *     gotenberg?: array{
 *         base_url?: scalar|null|Param, // Default: "http://gotenberg:3000"
 *         ping_cache_ttl?: scalar|null|Param, // Default: 60
 *     },
 *     dependency?: array{
 *         enabled?: scalar|null|Param, // Default: true
 *     },
 *     product_registration?: array{
 *         instance_identifier?: scalar|null|Param, // Unique identifier of that Pimcore instance. Will be generated during install.
 *         product_key?: scalar|null|Param, // Product registration key obtained during product registration. It is based on `instance_identifier` and `pimcore.encryption.secret`.
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
 *     ai?: AiConfig,
 *     pimcore_seo?: PimcoreSeoConfig,
 *     pimcore_static_routes?: PimcoreStaticRoutesConfig,
 *     pimcore_newsletter?: PimcoreNewsletterConfig,
 *     pimcore_open_search_client?: PimcoreOpenSearchClientConfig,
 *     pimcore_studio_ui?: PimcoreStudioUiConfig,
 *     pimcore_studio_backend?: PimcoreStudioBackendConfig,
 *     pimcore_generic_data_index?: PimcoreGenericDataIndexConfig,
 *     pimcore_generic_execution_engine?: PimcoreGenericExecutionEngineConfig,
 *     pimcore_admin?: PimcoreAdminConfig,
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
 *     core_shop_ai?: CoreShopAiConfig,
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
 *     debug?: DebugConfig,
 *     web_profiler?: WebProfilerConfig,
 *     presta_sitemap?: PrestaSitemapConfig,
 *     pimcore_static_resolver?: PimcoreStaticResolverConfig,
 *     pimcore_elasticsearch_client?: PimcoreElasticsearchClientConfig,
 *     mercure?: MercureConfig,
 *     webpack_encore?: WebpackEncoreConfig,
 *     knp_menu?: KnpMenuConfig,
 *     pimcore?: PimcoreConfig,
 *     "when@dev"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         ai?: AiConfig,
 *         pimcore_seo?: PimcoreSeoConfig,
 *         pimcore_static_routes?: PimcoreStaticRoutesConfig,
 *         pimcore_newsletter?: PimcoreNewsletterConfig,
 *         pimcore_open_search_client?: PimcoreOpenSearchClientConfig,
 *         pimcore_studio_ui?: PimcoreStudioUiConfig,
 *         pimcore_studio_backend?: PimcoreStudioBackendConfig,
 *         pimcore_generic_data_index?: PimcoreGenericDataIndexConfig,
 *         pimcore_generic_execution_engine?: PimcoreGenericExecutionEngineConfig,
 *         pimcore_admin?: PimcoreAdminConfig,
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
