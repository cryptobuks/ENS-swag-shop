import './polyfills';

import './assets/css/App.css';

import { isMobile } from 'react-device-detect';

// import ConnectButtons from './ConnectButtons';
import Profile from './Profile';

import {
  WagmiConfig,
  createClient,
  defaultChains,
  configureChains,
} from 'wagmi'

import { alchemyProvider } from 'wagmi/providers/alchemy'
import { publicProvider } from 'wagmi/providers/public'

import { CoinbaseWalletConnector } from 'wagmi/connectors/coinbaseWallet'
import { InjectedConnector } from 'wagmi/connectors/injected'
import { MetaMaskConnector } from 'wagmi/connectors/metaMask'
import { WalletConnectConnector } from 'wagmi/connectors/walletConnect'

// Configure chains & providers with the Alchemy provider.
// Two popular providers are Alchemy (alchemy.com) and Infura (infura.io)
const { chains, provider, webSocketProvider } = configureChains(defaultChains, [
  alchemyProvider({ apiKey: 'Q2jyuYfYMEpDvHR6oCs2Nnh-Wbh5llDk' }),
  publicProvider(),
])

// Set up client
const client = createClient({
  autoConnect: true,
  connectors: [
    new MetaMaskConnector({ chains }),
    new CoinbaseWalletConnector({
      chains,
      options: {
        appName: 'wagmi',
      },
    }),
    new WalletConnectConnector({
      chains,
      options: {
        qrcode: true,
      },
    }),
    new InjectedConnector({
      chains,
      options: {
        name: 'Injected',
        shimDisconnect: true,
      },
    }),
  ],
  provider,
  webSocketProvider,
});


function App() {

  const windowsWidth = isMobile ? 'mobile' : 'desktop';

  return (
    <div className={`App ${windowsWidth}`}>
      <WagmiConfig client={client}>
        <Profile />
      </WagmiConfig>
    </div>
  );
}

export default App;
