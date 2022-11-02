import React, { useState, useEffect } from 'react';

import { isBrowser, isMobile } from 'react-device-detect';


import {
    useAccount,
    useConnect,
    useDisconnect,
    useEnsAvatar,
    useEnsName,
    useSignMessage,
    useNetwork,
    useSwitchNetwork
} from 'wagmi'
import { verifyMessage } from 'ethers/lib/utils';

import { ApolloClient, InMemoryCache, gql } from '@apollo/client'
import { ethers } from "ethers";

export default function Profile() {

    const [modalOpen, setModal] = useState(false);
    const [modalProfileOpen, setModalProfile] = useState(false);
    const [copyAddress, setCopyAddress] = useState('Copy Address');

    const { address, connector, isConnected } = useAccount();
    const { chain } = useNetwork();
    const { switchNetwork, pendingChainId, isLoading: isLoadingSwitch } = useSwitchNetwork();

    const { data: ensAvatar } = useEnsAvatar({ addressOrName: address })
    const { data: ensName } = useEnsName({ address })
    const { connect, connectors, error, isLoading, pendingConnector } =
        useConnect()
    const { disconnect } = useDisconnect();

    const { data: dataSign, signMessage } = useSignMessage({
        onSuccess(data, variables) {
            // Verify signature when sign message succeeds
            const address_validate = verifyMessage(variables.message, data);
            if (address_validate === address) {// validation ok
                localStorage.setItem('ensSign', data);
                localStorage.setItem('ensHash', variables.message);
                window.dispatchEvent(new Event('storage.hashvalidate'));
            }
        },
        onError(error) {
            if (error.action === "signMessage" && error.code === "ACTION_REJECTED") {
                localStorage.setItem('ensSign', 'rejected');
                window.dispatchEvent(new Event('storage.hashrejected'));
            }
        },
    });


    const openModal = event => {
        event.preventDefault();
        setModal(true);
    }

    const closeModal = (event) => {
        if (event.target.className === 'walletModal') {
            setModal(false);
        }
        if (event.target.className === 'walletModalClose') {
            setModal(false);
        }
        if (event.target.className === 'walletModal profileModal') {
            setModal(false);
        }
    }

    const openModalProfile = event => {
        event.preventDefault();
        setModalProfile(true);
    }

    const closeModalProfile = (event) => {
        if (event.target.className === 'walletModalClose') {
            setModalProfile(false);
        }
        if (event.target.className === 'walletModal profileModal') {
            setModalProfile(false);
        }
    }

    // Signatue related stuff
    const getSignInMessageString = () => {
        return "Welcome to ENS Merch Store!\n\nBy signing this message, you accept our Terms of Service: https://ensmerchshop.xyz/terms/ and prove that you own this wallet address: " + address + ".\n\nThis request will not trigger a blockchain transaction or cost any gas fees.\n\nNonce: " + Math.floor(Date.now() / 1000);
    };

    // Request sign message after connection
    useEffect(() => {

        console.log({isBrowser});
        console.log({isMobile});

        if (isConnected && address && !isMobile) {
            window.dispatchEvent(new Event('storage.hash'));
            const message = getSignInMessageString();
            signMessage({ message });
        }
    }, [isConnected, address, signMessage]);

    // request sing in again
    const requestSignAgain = () => {
        if (isConnected && address) {
            window.dispatchEvent(new Event('storage.hash'));
            const message = getSignInMessageString();
            signMessage({ message });
        }
    };

    const copyToClipboard = (copyText) => {
        setCopyAddress('Copied!');
        navigator.clipboard.writeText(copyText);
        setTimeout(function () {
            setCopyAddress('Copy Address');
        }, 2000);
    }

    /* ENS domains getch related stuff */
    const APIURL = 'https://api.thegraph.com/subgraphs/name/ensdomains/ens';

    const getDomainAvatar = async (domain = null) => {

        if (typeof window.ethereum !== 'undefined') {
            const provider = new ethers.providers.JsonRpcProvider('https://eth-mainnet.g.alchemy.com/v2/p7uxxTdU6RCcnXpsrbqa29T8k9t0TjJl')
            const resolver = await provider.getResolver(domain);
            const avatar = await resolver.getAvatar();
            return avatar;
        }

        return null;

    };

    const getDomainObjectWithAvatar = async item => {
        const avatar_url = await getDomainAvatar(item.name);
        return {
            ...item,
            avatar_url: avatar_url
        }
    }

    const getDomainsAvatar = async (domainData) => {
        return Promise.all(domainData.map(item => getDomainObjectWithAvatar(item)));
    };

    useEffect(() => {    

        // Save main address in local storage
        localStorage.setItem('mainAddress', address);
        window.dispatchEvent(new Event('storage'));
    
        const getDomainsENS = async () => {
    
          const tokensQuery = `
            query {
              domains(first: 1000, where: {owner_: {id: "${address.toLowerCase()}"}}) {
                id
                name
                labelName
                labelhash
                resolvedAddress {
                  id
                }
                owner {
                  id
                }
              }
            }`;
    
          const client = new ApolloClient({
            uri: APIURL,
            cache: new InMemoryCache(),
          })
    
          client
            .query({
              query: gql(tokensQuery),
            })
            .then(async (data) => {
    
              let domainData = await getDomainsAvatar([...data.data.domains]);
    
              // Save ENS domains address in local storage
              localStorage.setItem('ensDomains', JSON.stringify(domainData));
    
              window.dispatchEvent(new Event('storage'));
            })
            .catch((err) => {
              console.log('Error fetching data: ', err)
            })
        }
    
        if (address) {
          getDomainsENS();
        }
    
      }, [address, getDomainsAvatar]);

    // User changed network request Etheruem
    if (chain && chain.unsupported) {
        return (
            <div className="switchNetworkHolder">
                <button id="switchNetworkBtn" onClick={openModal}>Switch network</button>
                <div className="walletModal walletModalSwith" onClick={closeModal}>
                    <div className="walletModalContent">
                        <h2>Switch Networks</h2>
                        <p>
                            Wrong network detected, switch or disconnect to continue.
                        </p>
                        <div className="walletModalButtons">
                            <button onClick={() => switchNetwork?.(1)} className="btnEtheruem">Ethereum {isLoadingSwitch && pendingChainId === 1 && <span>Confirm in Wallet</span>}</button>
                            <button onClick={disconnect} className="btnDisconnect">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>);
    }


    return (
        <div className="wagmiHolder">
            {isConnected && connector && (
                <>
                    <div className="profileHolder">

                        <button className="connectButtonName" onClick={openModalProfile} type="button">
                            {ensAvatar && <div className="imageHolder"><img src={ensAvatar} alt="avatar" /></div>}
                            {!ensAvatar && <div className="imageHolder default"><img src="https://ensmerchshop.xyz/wp-content/themes/ensswag/images/default-avatar.svg" alt="avatar" /></div>}

                            <div className="names">
                                <div className="name">{ensName ? ensName : `${address.substring(0, 4)}...${address.substring(address.length - 4, address.length)}`}</div>
                                {ensName && <div className="subname">{address.substring(0, 4)}...{address.substring(address.length - 4, address.length)}</div>}
                            </div>
                        </button>
                        <div className="delimiter"></div>
                        <button className="hidden" onClick={requestSignAgain}>request sign</button>
                    </div>
                    {isConnected && modalProfileOpen && (
                        <div className="walletModal profileModal" onClick={closeModalProfile}>
                            <div className="walletModalContent">
                                {ensAvatar && <div className="imageHolder"><img src={ensAvatar} alt="avatar" /></div>}
                                {!ensAvatar && <div className="imageHolder default"><img src="https://ensmerchshop.xyz/wp-content/themes/ensswag/images/default-avatar.svg" alt="avatar" /></div>}
                                <div className="names">
                                    <div className="name">{ensName ? ensName : `${address.substring(0, 4)}...${address.substring(address.length - 4, address.length)}`}</div>
                                </div>
                                <button className="walletModalClose" onClick={closeModalProfile}>Close Modal</button>
                                <div className="buttonsHolder">
                                    <button className="btnCopy" onClick={() => copyToClipboard(address)}>{copyAddress}</button>
                                    <button className="btnDisconnect" onClick={disconnect}>Disconnect</button>
                                </div>
                            </div>
                        </div>
                    )}
                </>
            )}

            {!isConnected && <div className="myBtnHolder"><button id="myBtn" onClick={openModal}>Connect Wallet</button></div>}

            {!isConnected && modalOpen && <div className="walletModal" onClick={closeModal}><div className="walletModalContent">{
                connectors.map((connector) => (
                    <button
                        disabled={!connector.ready}
                        key={connector.id}
                        onClick={() => connect({ connector })}
                        className={`${connector.name.replace(/\s+/g, '')} ${!connector.ready ? 'unsupported' : ''}`}
                    >
                        {connector.name}
                        {!connector.ready && ' (unsupported)'}
                        {isLoading &&
                            connector.id === pendingConnector?.id &&
                            ' (connecting)'}
                    </button>))
            }<button className="walletModalClose" onClick={closeModal}>Close Modal</button></div></div>}
        </div>
    )
}