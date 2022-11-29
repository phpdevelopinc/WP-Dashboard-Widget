import React from "react";
import ReactDOM from "react-dom";
import Dashboard from './components/Dashboard';

/* The App class is the parent component that renders the Dashboard component. It also has a state that
holds the value of the dropdown menu. The handleChange function updates the state with the value of
the dropdown menu. The fetchChart function returns the Dashboard component with a filter prop that
is equal to the value of the dropdown menu */

class App extends React.Component {

    state = {
      type: 0
    }
  
    handleChange = (e) => {
      this.setState({type: e.target.value})
    }
  
    fetchChart = () => {
      const {type} = this.state
      switch(type){
        case "7 Days": return <Dashboard key={type} filter={7}/>
        case "15 days": return <Dashboard key={type} filter={15}/>
        case "1 month": return <Dashboard key={type} filter={1}/>
        default: return <Dashboard key={type} filter={0} />
      }
    }
    render(){
      return (
        <div>
            <div className = 'row'>
                <div className = 'column'><h2 className='app-title'>Graph Widget</h2></div>
                <div className = 'column' style={{paddingTop:"8px"}}>
                    <select style={{float: "inline-end"}} onChange={this.handleChange}>
                        <option>All</option>
                        <option>7 Days</option>
                        <option>15 days</option>
                        <option>1 month</option>
                    </select>
                </div>
            </div>
            <hr />
            {this.fetchChart()}
        </div>
      )
    }
  }

export default App; 