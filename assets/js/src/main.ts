import { Pimcore } from 'pimcore-studio-ui'
import { MyFirstTabComponent } from './components/my-first-tab-component'

Pimcore.pluginSystem.registerPlugin({
    name: 'pimcore-demo-plugin',

    // Register and overwrite services here
    onInit: ({ container }): void => {
        console.log('Init my plugin')
    },

    // register modules here
    onStartup: ({ moduleSystem }): void => {
        console.log('Start up my plugin');
        // moduleSystem.registerModule(MyFirstTabComponent)
    }
})
