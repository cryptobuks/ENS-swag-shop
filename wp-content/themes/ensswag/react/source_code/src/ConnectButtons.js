import { useConnect } from 'wagmi';

const ConnectButtons = () => {
    const { connect, connectors, error, isLoading, pendingConnector } =
    useConnect()

    console.log({connectors});

  return (
    <div>
      {connectors.map((connector) => { 

        console.log({connector});
        
        return (
        <button
          disabled={!connector.ready}
          key={connector.id}
          onClick={() => connect({ connector })}
        >
          A{connector && connector.name}
          {!connector.ready && ' (unsupported)'}
          {isLoading &&
            connector.id === pendingConnector?.id &&
            ' (connecting)'}
        </button>
      )})}

      {error && <div>{error.message}</div>}
    </div>
  )
};

export default ConnectButtons;